<?php

namespace App\Livewire\Frontend;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleReview;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReviewForm extends Component
{
    use WithFileUploads;

    public Vehicle $vehicle;

    public int $rating = 0;

    public ?int $cleanlinessRating = null;

    public ?int $serviceRating = null;

    public ?int $conditionRating = null;

    public ?int $valueRating = null;

    public string $comment = '';

    public array $photos = [];

    public array $photoPreviews = [];

    public bool $hasCompletedBooking = false;

    public bool $alreadyReviewed = false;

    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'cleanlinessRating' => 'nullable|integer|min:1|max:5',
            'serviceRating' => 'nullable|integer|min:1|max:5',
            'conditionRating' => 'nullable|integer|min:1|max:5',
            'valueRating' => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string|min:20|max:2000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'nullable|image|max:5120',
        ];
    }

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;

        if (! auth()->check()) {
            return;
        }

        $customer = auth()->user()->customer;

        if (! $customer) {
            return;
        }

        $this->hasCompletedBooking = Booking::where('customer_id', $customer->id)
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'completed')
            ->exists();

        $this->alreadyReviewed = VehicleReview::where('customer_id', $customer->id)
            ->where('vehicle_id', $vehicle->id)
            ->exists();
    }

    public function updatedPhotos(): void
    {
        $this->validate(['photos.*' => 'image|max:5120']);

        $this->photoPreviews = [];

        foreach ($this->photos as $photo) {
            $this->photoPreviews[] = $photo->temporaryUrl();
        }
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index], $this->photoPreviews[$index]);
        $this->photos = array_values($this->photos);
        $this->photoPreviews = array_values($this->photoPreviews);
    }

    public function submit(): void
    {
        $this->validate();

        $customer = auth()->user()->customer;

        $review = VehicleReview::create([
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $customer->id,
            'rating' => $this->rating,
            'cleanliness_rating' => $this->cleanlinessRating,
            'service_rating' => $this->serviceRating,
            'condition_rating' => $this->conditionRating,
            'value_rating' => $this->valueRating,
            'comment' => $this->comment,
            'is_verified_booking' => $this->hasCompletedBooking,
            'is_approved' => false,
        ]);

        if (! empty($this->photos)) {
            $paths = [];

            foreach ($this->photos as $photo) {
                $paths[] = $photo->store('review-photos', 'public');
            }

            $review->update(['photos' => $paths]);
        }

        $this->submitted = true;
        $this->dispatch('review-submitted');
    }

    public function render()
    {
        return view('livewire.frontend.review-form');
    }
}
