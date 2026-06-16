<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use Livewire\Component;

class VehicleGallery extends Component
{
    public Vehicle $vehicle;

    public array $images = [];

    public int $activeIndex = 0;

    public bool $lightboxOpen = false;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->images = $this->getImages();
    }

    public function setActive(int $index): void
    {
        $this->activeIndex = $index;
    }

    public function openLightbox(int $index): void
    {
        $this->activeIndex = $index;
        $this->lightboxOpen = true;
    }

    public function closeLightbox(): void
    {
        $this->lightboxOpen = false;
    }

    public function prevImage(): void
    {
        $this->activeIndex = $this->activeIndex > 0 ? $this->activeIndex - 1 : count($this->images) - 1;
    }

    public function nextImage(): void
    {
        $this->activeIndex = $this->activeIndex < count($this->images) - 1 ? $this->activeIndex + 1 : 0;
    }

    private function getImages(): array
    {
        $images = is_array($this->vehicle->images) ? $this->vehicle->images : (json_decode($this->vehicle->images, true) ?? []);

        if ($this->vehicle->image_url) {
            array_unshift($images, $this->vehicle->image_url);
        }

        return array_values(array_unique($images));
    }

    public function render()
    {
        return view('livewire.frontend.vehicle-gallery');
    }
}
