<?php

namespace Tests\Feature\Admin;

use App\Filament\Forms\Components\HotspotPicker;
use App\Rules\Hotspots;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/** Points posés sur la photo du studio : positions en %, libellé court, six au maximum. */
class HotspotPickerTest extends TestCase
{
    public function test_state_is_normalised(): void
    {
        $this->assertSame(
            [['x' => 48.6, 'y' => 50.0, 'label' => 'Console']],
            HotspotPicker::normalize([
                ['x' => '48.56', 'y' => 50, 'label' => ' Console '],
                ['x' => 10, 'y' => 10, 'label' => ''],
            ]),
        );
        $this->assertSame([], HotspotPicker::normalize(null));
    }

    public function test_more_than_six_points_is_rejected(): void
    {
        $points = array_fill(0, 7, ['x' => 10, 'y' => 10, 'label' => 'Micro']);

        $validator = Validator::make(['hotspots' => $points], ['hotspots' => [new Hotspots]]);

        $this->assertTrue($validator->fails());
        $this->assertSame('6 points au maximum.', $validator->errors()->first('hotspots'));
    }

    public function test_label_longer_than_40_characters_is_rejected(): void
    {
        $validator = Validator::make(['hotspots' => [['x' => 10, 'y' => 10, 'label' => str_repeat('a', 41)]]], ['hotspots' => [new Hotspots]]);

        $this->assertTrue($validator->fails());
        $this->assertSame('Chaque libellé fait 40 caractères au maximum.', $validator->errors()->first('hotspots'));
    }

    public function test_a_point_without_label_is_rejected_with_its_number(): void
    {
        $validator = Validator::make(['hotspots' => [
            ['x' => 10, 'y' => 10, 'label' => 'Console'],
            ['x' => 20, 'y' => 20, 'label' => '  '],
        ]], ['hotspots' => [new Hotspots]]);

        $this->assertTrue($validator->fails());
        $this->assertSame('Écrivez ce que montre le point 2 (ou supprimez-le).', $validator->errors()->first('hotspots'));
    }

    public function test_six_valid_points_pass(): void
    {
        $points = array_fill(0, 6, ['x' => 99.9, 'y' => 0, 'label' => str_repeat('a', 40)]);

        $this->assertFalse(Validator::make(['hotspots' => $points], ['hotspots' => [new Hotspots]])->fails());
    }
}
