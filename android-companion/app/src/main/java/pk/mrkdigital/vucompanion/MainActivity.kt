package pk.mrkdigital.vucompanion

import android.app.Activity
import android.graphics.Color
import android.os.Bundle
import android.webkit.SslErrorHandler
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.net.http.SslError
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.ScrollView
import android.widget.Spinner
import android.widget.TextView
import android.widget.Toast
import org.json.JSONArray
import org.json.JSONObject
import org.json.JSONTokener
import java.net.HttpURLConnection
import java.net.URL
import java.util.UUID
import kotlin.concurrent.thread

class MainActivity : Activity() {
    private lateinit var webView: WebView
    private lateinit var portalInput: EditText
    private lateinit var tokenInput: EditText
    private lateinit var courseCodeInput: EditText
    private lateinit var courseTitleInput: EditText
    private lateinit var titleInput: EditText
    private lateinit var dueInput: EditText
    private lateinit var detailsInput: EditText
    private lateinit var typeSpinner: Spinner
    private lateinit var pagePreview: TextView
    private lateinit var statusText: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        buildInterface()
        openVulms()
    }

    private fun buildInterface() {
        val root = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setBackgroundColor(Color.rgb(8, 24, 42))
            setPadding(12, 10, 12, 8)
        }

        val heading = TextView(this).apply {
            text = "MRK VU Companion"
            textSize = 20f
            setTextColor(Color.WHITE)
            setPadding(4, 4, 4, 8)
        }
        root.addView(heading)

        val toolbar = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL }
        val openButton = Button(this).apply {
            text = "Open VULMS"
            setOnClickListener { openVulms() }
        }
        val extractButton = Button(this).apply {
            text = "Read visible page"
            setOnClickListener { readVisiblePage() }
        }
        toolbar.addView(openButton, LinearParams(0, 48, 1f))
        toolbar.addView(extractButton, LinearParams(0, 48, 1f))
        root.addView(toolbar)

        webView = WebView(this).apply {
            setBackgroundColor(Color.WHITE)
            settings.javaScriptEnabled = true
            settings.domStorageEnabled = true
            settings.databaseEnabled = false
            settings.allowFileAccess = false
            settings.allowContentAccess = false
            settings.javaScriptCanOpenWindowsAutomatically = false
            settings.setSupportMultipleWindows(false)
            settings.mixedContentMode = WebSettings.MIXED_CONTENT_NEVER_ALLOW
            settings.safeBrowsingEnabled = true
            webViewClient = object : WebViewClient() {
                override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                    val host = request?.url?.host?.lowercase() ?: return true
                    return !(host == "vu.edu.pk" || host.endsWith(".vu.edu.pk"))
                }

                override fun onReceivedSslError(view: WebView?, handler: SslErrorHandler?, error: SslError?) {
                    handler?.cancel()
                }
            }
        }
        root.addView(webView, LinearParams(LinearParams.MATCH_PARENT, 0, 1f))

        val formScroll = ScrollView(this)
        val form = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(4, 8, 4, 8)
        }

        form.addView(label("MRK portal HTTPS address"))
        portalInput = field("https://your-mrk-portal.example")
        form.addView(portalInput)

        form.addView(label("Personal Companion API token"))
        tokenInput = field("Paste token generated in MRK VU Study")
        tokenInput.inputType = android.text.InputType.TYPE_CLASS_TEXT or android.text.InputType.TYPE_TEXT_VARIATION_PASSWORD
        form.addView(tokenInput)

        form.addView(label("Course code"))
        courseCodeInput = field("CS101")
        form.addView(courseCodeInput)

        form.addView(label("Course name"))
        courseTitleInput = field("Introduction to Computing")
        form.addView(courseTitleInput)

        form.addView(label("Activity type"))
        typeSpinner = Spinner(this).apply {
            adapter = ArrayAdapter(
                this@MainActivity,
                android.R.layout.simple_spinner_dropdown_item,
                listOf("announcement", "assignment", "quiz", "gdb", "exam", "grade")
            )
        }
        form.addView(typeSpinner)

        form.addView(label("Title"))
        titleInput = field("Quiz 3")
        form.addView(titleInput)

        form.addView(label("Deadline (YYYY-MM-DD HH:MM), optional"))
        dueInput = field("2026-12-01 23:59")
        form.addView(dueInput)

        form.addView(label("Details (reviewed text only)"))
        detailsInput = field("Enter a short summary; do not paste passwords or private data.")
        detailsInput.minLines = 2
        detailsInput.maxLines = 4
        form.addView(detailsInput)

        val sendButton = Button(this).apply {
            text = "Send reviewed item to MRK"
            setOnClickListener { sendReviewedItem() }
        }
        form.addView(sendButton)

        pagePreview = TextView(this).apply {
            text = "Page text preview stays on this device and is not sent to MRK."
            textSize = 12f
            setTextColor(Color.LTGRAY)
            setPadding(4, 8, 4, 8)
        }
        form.addView(pagePreview)

        statusText = TextView(this).apply {
            text = "Sign in to VULMS directly. No VULMS password is sent to MRK."
            textSize = 12f
            setTextColor(Color.rgb(130, 210, 255))
            setPadding(4, 8, 4, 8)
        }
        form.addView(statusText)

        formScroll.addView(form)
        root.addView(formScroll, LinearParams(LinearParams.MATCH_PARENT, 290))

        setContentView(root)
    }

    private fun openVulms() {
        webView.loadUrl("https://vulms.vu.edu.pk/")
    }

    private fun readVisiblePage() {
        statusText.text = "Reading visible page locally…"
        val script = "(function(){return JSON.stringify({title:document.title||'',text:(document.body&&document.body.innerText)||''});})()"
        webView.evaluateJavascript(script) { result ->
            try {
                val decoded = JSONTokener(result ?: "\"\"").nextValue() as? String ?: ""
                val page = JSONObject(decoded)
                val visibleText = page.optString("text").take(12000)
                pagePreview.text = "LOCAL PREVIEW — not uploaded\n" +
                    page.optString("title") + "\n\n" + visibleText
                statusText.text = "Page text read on this phone. Review it and enter one study item below."
            } catch (_: Exception) {
                statusText.text = "Could not read this page. Open a VULMS page and try again."
            }
        }
    }

    private fun sendReviewedItem() {
        val portal = portalInput.text.toString().trim().trimEnd('/')
        val token = tokenInput.text.toString().trim()
        val courseCode = courseCodeInput.text.toString().trim()
        val courseTitle = courseTitleInput.text.toString().trim()
        val title = titleInput.text.toString().trim()
        val type = typeSpinner.selectedItem?.toString() ?: "announcement"
        val due = dueInput.text.toString().trim()
        val details = detailsInput.text.toString().trim()

        if (!portal.startsWith("https://")) {
            toast("Enter your MRK portal HTTPS address.")
            return
        }
        if (token.length < 32) {
            toast("Paste the personal API token from MRK VU Study.")
            return
        }
        if (title.isBlank()) {
            toast("Enter the study item title.")
            return
        }
        if (due.isNotBlank() && !due.matches(Regex("""\d{4}-\d{2}-\d{2} \d{2}:\d{2}"""))) {
            toast("Use deadline format YYYY-MM-DD HH:MM.")
            return
        }

        val item = JSONObject().apply {
            put("course_code", courseCode)
            put("course_title", courseTitle)
            put("item_type", type)
            put("title", title)
            put("source_key", "android-" + UUID.randomUUID().toString())
            if (details.isNotBlank()) put("description", details)
            if (due.isNotBlank()) put("due_at", due)
            put("metadata", JSONObject().put("capture_method", "student_reviewed_webview_form"))
        }
        val payload = JSONObject().put("items", JSONArray().put(item))

        statusText.text = "Sending only the reviewed study item to MRK…"
        thread {
            var connection: HttpURLConnection? = null
            try {
                val endpoint = URL("$portal/api/vu-study/sync")
                connection = endpoint.openConnection() as HttpURLConnection
                connection.requestMethod = "POST"
                connection.connectTimeout = 12000
                connection.readTimeout = 18000
                connection.doOutput = true
                connection.setRequestProperty("Accept", "application/json")
                connection.setRequestProperty("Content-Type", "application/json; charset=utf-8")
                connection.setRequestProperty("Authorization", "Bearer $token")
                connection.outputStream.use { it.write(payload.toString().toByteArray(Charsets.UTF_8)) }

                val code = connection.responseCode
                val stream = if (code in 200..299) connection.inputStream else connection.errorStream
                val response = stream?.bufferedReader()?.use { it.readText() }.orEmpty()
                runOnUiThread {
                    if (code in 200..299) {
                        statusText.text = "Saved to MRK. Review it in the VU Study dashboard."
                        toast("Study item synced.")
                        titleInput.text.clear()
                        dueInput.text.clear()
                        detailsInput.text.clear()
                    } else {
                        statusText.text = "MRK returned HTTP $code. Check portal address, token and database migration."
                        toast("Sync failed (HTTP $code).")
                    }
                }
            } catch (error: Exception) {
                runOnUiThread {
                    statusText.text = "Connection failed: \${error.message ?: "network error"}"
                    toast("Could not connect to MRK.")
                }
            } finally {
                connection?.disconnect()
            }
        }
    }

    private fun label(value: String) = TextView(this).apply {
        text = value
        textSize = 13f
        setTextColor(Color.WHITE)
        setPadding(4, 8, 4, 2)
    }

    private fun field(hintText: String) = EditText(this).apply {
        hint = hintText
        textSize = 14f
        setTextColor(Color.WHITE)
        setHintTextColor(Color.GRAY)
        setSingleLine(false)
        setPadding(12, 8, 12, 8)
        setBackgroundColor(Color.rgb(25, 43, 62))
    }

    private fun toast(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }

    private fun LinearParams(width: Int, height: Int, weight: Float = 0f) =
        LinearLayout.LayoutParams(width, height, weight)

    override fun onDestroy() {
        webView.stopLoading()
        webView.destroy()
        tokenInput.setText("")
        super.onDestroy()
    }
}
