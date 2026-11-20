<?php

namespace Tests\Feature;

use Tests\TestCase;

class QuranMurajaahTest extends TestCase
{
    public function test_murajaah_page_exposes_current_offline_recitation_controls(): void
    {
        $this->get('/quran/murajaah')
            ->assertOk()
            ->assertSee('حفظِ قرآن — آف لائن دہرائی')
            ->assertSee('آف لائن ماڈل لوڈ کریں')
            ->assertSee('مائیک کی اجازت چیک کریں')
            ->assertSee('تلاوت شروع کریں')
            ->assertSee('js/quran-murajaah-offline.js', false);
    }
}
