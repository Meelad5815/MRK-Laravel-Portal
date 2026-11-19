<?php

namespace Tests\Feature;

use Tests\TestCase;

class QuranMurajaahTest extends TestCase
{
    public function test_murajaah_page_is_online_and_exposes_microphone_controls(): void
    {
        $this->get('/quran/murajaah')
            ->assertOk()
            ->assertSee('آن لائن دہرائی')
            ->assertSee('مائیک کی اجازت چیک کریں')
            ->assertSee('تلاوت شروع کریں')
            ->assertDontSee('آف لائن ماڈل لوڈ کریں')
            ->assertSee('js/quran-murajaah-online.js', false);
    }
}
