package com.mrk.vustudy

import android.app.Activity
import android.app.AlertDialog
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.util.Base64
import android.view.View
import android.view.ViewGroup
import android.webkit.JavascriptInterface
import android.webkit.WebChromeClient
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
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
import java.net.HttpURLConnection
import java.net.URL
import java.util.Locale
import java.util.concurrent.Executors
import javax.crypto.Cipher
import javax.crypto.KeyGenerator
import javax.crypto.SecretKey
import javax.crypto.spec.GCMParameterSpec
import java.security.KeyStore
import android.security.keystore.KeyGenParameterSpec
import android.security.keystore.KeyProperties

class MainActivity : Activity() {
    private val apiBase = "https://meeladrazakhokhar786.freehosting.dev"
    private val executor = Executors.newSingleThreadExecutor()
    private lateinit var root: LinearLayout
    private lateinit var status: TextView
    private lateinit var tokenInput: EditText
    private lateinit var webView: WebView
    private lateinit var homePanel: LinearLayout
    private lateinit var homeScroll: ScrollView
    private var token = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        token = readToken().orEmpty()
        buildScreen()
    }

    private fun buildScreen() {
        root = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setBackgroundColor(0xFFF4F7FB.toInt())
            layoutParams = ViewGroup.LayoutParams(-1, -1)
        }
        val header = TextView(this).apply {
            text = "MRK VU STUDY\nVirtual University Companion"
            textSize = 21f
            setTextColor(0xFFFFFFFF.toInt())
            setPadding(22, 24, 22, 22)
            setBackgroundColor(0xFF082B5B.toInt())
        }
        root.addView(header)
        homePanel = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(20, 20, 20, 20)
        }
        homeScroll = ScrollView(this)
        homeScroll.addView(homePanel)
        root.addView(homeScroll, LinearLayout.LayoutParams(-1, 0, 1f))

        homePanel.addView(label("محفوظ اور آسان VULMS Study Sync"))
        homePanel.addView(label("1) MRK Dashboard میں Companion Token بنائیں۔\n2) Token یہاں محفوظ کریں۔\n3) VULMS کھولیں، سرکاری VULMS صفحے پر لاگ اِن کریں، پھر MRK میں بھیجیں دبائیں۔"))
        tokenInput = EditText(this).apply {
            hint = "MRK Companion Token"
            setSingleLine(true)
            inputType = 0x00000081
            setText(token)
        }
        homePanel.addView(tokenInput, fieldParams())
        homePanel.addView(actionButton("Token محفوظ کریں") { saveToken() })
        homePanel.addView(actionButton("VULMS کھولیں") {
            if (token.isBlank()) toast("پہلے MRK Companion Token محفوظ کریں۔") else openVulms()
        })
        homePanel.addView(actionButton("MRK Study Dashboard") {
            startActivity(Intent(Intent.ACTION_VIEW, Uri.parse("$apiBase/vu-study")))
        })
        homePanel.addView(label("رازداری: آپ کا VULMS پاس ورڈ اس ایپ یا MRK سرور کو نہیں بھیجا جاتا۔ آپ VULMS میں براہِ راست لاگ اِن کرتے ہیں۔ ایپ صرف آپ کے منتخب کردہ کھلے صفحے کا نظر آنے والا متن MRK کو بھیجتی ہے۔"))
        status = label("حالت: تیار")
        homePanel.addView(status)

        webView = WebView(this).apply {
            visibility = View.GONE
            settings.javaScriptEnabled = true
            settings.domStorageEnabled = true
            settings.allowFileAccess = false
            settings.allowContentAccess = false
            settings.javaScriptCanOpenWindowsAutomatically = false
            settings.setSupportMultipleWindows(false)
            if (android.os.Build.VERSION.SDK_INT >= 26) settings.safeBrowsingEnabled = true
            addJavascriptInterface(PageBridge(), "MRKBridge")
            webChromeClient = WebChromeClient()
            webViewClient = object : WebViewClient() {
                override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
                    val uri = request.url
                    val host = uri.host.orEmpty().lowercase(Locale.ROOT)
                    val isVu = uri.scheme == "https" && (host == "vu.edu.pk" || host.endsWith(".vu.edu.pk"))
                    if (isVu) return false
                    toast("صرف سرکاری VU ویب سائٹ اس ایپ میں کھولی جا سکتی ہے۔")
                    return true
                }

                override fun onPageFinished(view: WebView, url: String) {
                    super.onPageFinished(view, url)
                    val host = runCatching { URL(url).host.lowercase(Locale.ROOT) }.getOrDefault("")
                    if (host == "vu.edu.pk" || host.endsWith(".vu.edu.pk")) {
                        view.evaluateJavascript(INJECT_CAPTURE_BUTTON, null)
                    }
                }
            }
        }
        root.addView(webView, LinearLayout.LayoutParams(-1, 0, 1f))
        setContentView(root)
    }

    private fun label(value: String) = TextView(this).apply {
        text = value
        textSize = 15f
        setTextColor(0xFF18324D.toInt())
        setPadding(0, 10, 0, 14)
    }

    private fun fieldParams() = LinearLayout.LayoutParams(-1, ViewGroup.LayoutParams.WRAP_CONTENT).apply {
        bottomMargin = 12
    }

    private fun actionButton(textValue: String, action: () -> Unit) = Button(this).apply {
        text = textValue
        isAllCaps = false
        setOnClickListener { action() }
        layoutParams = fieldParams()
    }

    private fun saveToken() {
        val value = tokenInput.text.toString().trim()
        if (value.length < 40) {
            toast("درست Companion Token پیسٹ کریں۔")
            return
        }
        runCatching { writeToken(value) }.onSuccess {
            token = value
            status.text = "حالت: Token محفوظ ہو گیا۔"
            toast("Token محفوظ ہو گیا۔")
        }.onFailure {
            toast("Token محفوظ نہیں ہو سکا: " + it.message)
        }
    }

    private fun openVulms() {
        homeScroll.visibility = View.GONE
        webView.visibility = View.VISIBLE
        if (webView.url.isNullOrBlank()) webView.loadUrl("https://vulms.vu.edu.pk/")
    }

    private fun showHome() {
        webView.visibility = View.GONE
        homeScroll.visibility = View.VISIBLE
    }

    private fun confirmPage(titleValue: String, pageText: String, pageUrl: String) {
        val host = runCatching { URL(pageUrl).host.lowercase(Locale.ROOT) }.getOrDefault("")
        if (!(host == "vu.edu.pk" || host.endsWith(".vu.edu.pk"))) {
            toast("صرف VU کا صفحہ بھیجا جا سکتا ہے۔")
            return
        }
        val cleanTitle = titleValue.ifBlank { pageUrl.substringAfterLast('/').ifBlank { "VULMS Page" } }.take(255)
        val kindOptions = arrayOf("Announcement", "Assignment", "Quiz", "GDB", "Exam", "Grade")
        val typeKeys = arrayOf("announcement", "assignment", "quiz", "gdb", "exam", "grade")
        val pageHint = cleanTitle + " " + pageUrl
        val suggested = when {
            pageHint.contains("assignment", true) -> 1
            pageHint.contains("quiz", true) -> 2
            pageHint.contains("gdb", true) || pageHint.contains("discussion", true) -> 3
            pageHint.contains("exam", true) -> 4
            pageHint.contains("grade", true) -> 5
            else -> 0
        }
        val panel = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(18, 8, 18, 0)
        }
        val typeSpinner = Spinner(this).apply {
            adapter = ArrayAdapter(this@MainActivity, android.R.layout.simple_spinner_dropdown_item, kindOptions)
            setSelection(suggested)
        }
        val title = EditText(this).apply { setText(cleanTitle); hint = "عنوان" }
        val course = EditText(this).apply { hint = "Course Code (مثلاً CS101)" }
        val due = EditText(this).apply { hint = "آخری تاریخ (اختیاری: YYYY-MM-DD)" }
        val description = EditText(this).apply {
            hint = "صفحے کی تفصیل"
            minLines = 4
            maxLines = 7
            setText(pageText.take(5000))
        }
        panel.addView(label("محفوظ کرنے سے پہلے معلومات چیک کریں۔"))
        panel.addView(typeSpinner)
        panel.addView(title)
        panel.addView(course)
        panel.addView(due)
        panel.addView(description)
        val dialogScroll = ScrollView(this).apply { addView(panel) }
        AlertDialog.Builder(this)
            .setTitle("MRK Study میں بھیجیں")
            .setView(dialogScroll)
            .setNegativeButton("منسوخ", null)
            .setPositiveButton("محفوظ کریں") { _, _ ->
                val selectedTitle = title.text.toString().trim()
                val selectedDue = due.text.toString().trim()
                if (selectedTitle.isBlank()) {
                    toast("عنوان لکھیں۔")
                    return@setPositiveButton
                }
                val descriptionText = description.text.toString().take(5000)
                val item = JSONObject().apply {
                    put("item_type", typeKeys[typeSpinner.selectedItemPosition])
                    put("title", selectedTitle)
                    put("course_code", course.text.toString().trim().uppercase(Locale.ROOT))
                    put("description", descriptionText)
                    put("source_url", pageUrl)
                    put("source_key", "android-" + sha256(pageUrl + "|" + selectedTitle + "|" + descriptionText.take(500)))
                    if (selectedDue.matches(Regex("\\d{4}-\\d{2}-\\d{2}"))) {
                        put("due_at", selectedDue + " 23:59:00")
                    }
                }
                sendToMrk(item)
            }
            .show()
    }

    private fun sendToMrk(item: JSONObject) {
        val currentToken = token.ifBlank { readToken().orEmpty() }
        if (currentToken.isBlank()) {
            toast("پہلے Companion Token محفوظ کریں۔")
            return
        }
        status.text = "حالت: MRK کو بھیجا جا رہا ہے…"
        executor.execute {
            var connection: HttpURLConnection? = null
            try {
                connection = (URL("$apiBase/api/vu-study/sync").openConnection() as HttpURLConnection).apply {
                    requestMethod = "POST"
                    connectTimeout = 20000
                    readTimeout = 20000
                    doOutput = true
                    setRequestProperty("Authorization", "Bearer $currentToken")
                    setRequestProperty("Content-Type", "application/json; charset=utf-8")
                    setRequestProperty("Accept", "application/json")
                }
                val payload = JSONObject().put("items", JSONArray().put(item)).toString()
                connection.outputStream.use { it.write(payload.toByteArray(Charsets.UTF_8)) }
                val code = connection.responseCode
                val stream = if (code in 200..299) connection.inputStream else connection.errorStream
                val response = stream?.bufferedReader()?.use { it.readText() }.orEmpty()
                if (code in 200..299) {
                    runOnUiThread {
                        status.text = "حالت: کامیابی — MRK میں محفوظ ہو گیا۔"
                        toast("VULMS کی معلومات MRK Study میں محفوظ ہو گئیں۔")
                    }
                } else {
                    val message = runCatching { JSONObject(response).optString("message") }.getOrDefault(response)
                    runOnUiThread {
                        status.text = "حالت: Sync ناکام ($code)"
                        toast("Sync ناکام: " + message.take(180))
                    }
                }
            } catch (error: Exception) {
                runOnUiThread {
                    status.text = "حالت: انٹرنیٹ یا سرور کا مسئلہ"
                    toast("رابطہ نہیں ہو سکا: " + error.message)
                }
            } finally {
                connection?.disconnect()
            }
        }
    }

    private fun sha256(value: String): String {
        val bytes = java.security.MessageDigest.getInstance("SHA-256").digest(value.toByteArray())
        return bytes.joinToString("") { "%02x".format(it) }
    }

    private fun toast(message: String) = Toast.makeText(this, message, Toast.LENGTH_LONG).show()

    private fun writeToken(value: String) {
        val cipher = Cipher.getInstance("AES/GCM/NoPadding")
        cipher.init(Cipher.ENCRYPT_MODE, getOrCreateKey())
        val encrypted = cipher.doFinal(value.toByteArray(Charsets.UTF_8))
        getSharedPreferences("mrk_secure", MODE_PRIVATE).edit()
            .putString("token_iv", Base64.encodeToString(cipher.iv, Base64.NO_WRAP))
            .putString("token_data", Base64.encodeToString(encrypted, Base64.NO_WRAP))
            .apply()
    }

    private fun readToken(): String? = runCatching {
        val prefs = getSharedPreferences("mrk_secure", MODE_PRIVATE)
        val iv = Base64.decode(prefs.getString("token_iv", null), Base64.NO_WRAP)
        val data = Base64.decode(prefs.getString("token_data", null), Base64.NO_WRAP)
        val cipher = Cipher.getInstance("AES/GCM/NoPadding")
        cipher.init(Cipher.DECRYPT_MODE, getOrCreateKey(), GCMParameterSpec(128, iv))
        String(cipher.doFinal(data), Charsets.UTF_8)
    }.getOrNull()

    private fun getOrCreateKey(): SecretKey {
        val store = KeyStore.getInstance("AndroidKeyStore").apply { load(null) }
        val alias = "MRK_VU_COMPANION_TOKEN"
        val existing = store.getKey(alias, null) as? SecretKey
        if (existing != null) return existing
        val generator = KeyGenerator.getInstance(KeyProperties.KEY_ALGORITHM_AES, "AndroidKeyStore")
        generator.init(
            KeyGenParameterSpec.Builder(
                alias,
                KeyProperties.PURPOSE_ENCRYPT or KeyProperties.PURPOSE_DECRYPT
            ).setBlockModes(KeyProperties.BLOCK_MODE_GCM)
                .setEncryptionPaddings(KeyProperties.ENCRYPTION_PADDING_NONE)
                .setRandomizedEncryptionRequired(true)
                .build()
        )
        return generator.generateKey()
    }

    inner class PageBridge {
        @JavascriptInterface
        fun sendPage(title: String, text: String, url: String) {
            runOnUiThread { confirmPage(title, text, url) }
        }
    }

    override fun onBackPressed() {
        if (::webView.isInitialized && webView.visibility == View.VISIBLE) {
            if (webView.canGoBack()) webView.goBack() else showHome()
        } else {
            super.onBackPressed()
        }
    }

    override fun onDestroy() {
        executor.shutdown()
        if (::webView.isInitialized) webView.destroy()
        super.onDestroy()
    }

    companion object {
        private const val INJECT_CAPTURE_BUTTON = """
            (function(){
              if(document.getElementById('mrk-vu-capture')) return;
              var b=document.createElement('button');
              b.id='mrk-vu-capture';
              b.textContent='MRK میں بھیجیں';
              b.style.cssText='position:fixed;z-index:2147483647;bottom:18px;right:14px;background:#082b5b;color:#fff;border:2px solid #fff;border-radius:28px;padding:13px 18px;font-size:16px;font-weight:700;box-shadow:0 3px 14px #0008;';
              b.onclick=function(){
                var t=(document.body && document.body.innerText || '').slice(0,12000);
                window.MRKBridge.sendPage(document.title || '',t,location.href);
              };
              document.body.appendChild(b);
            })();
        """
    }
}
