<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class BookingBar extends Component
{
    public string $price = '0';

    public string $currency = 'DH';

    public string $bookUrl = '#';

    public bool $visible = false;

    protected $listeners = ['update-booking-bar' => 'update'];

    public function update(string $price, string $bookUrl): void
    {
        $this->price = $price;
        $this->bookUrl = $bookUrl;
        $this->visible = true;
    }

    public function render()
    {
        return view('livewire.frontend.booking-bar');
    }
}
