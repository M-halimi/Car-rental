<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use App\Models\VehicleReview;
use Livewire\Component;

class ReviewList extends Component
{
    public Vehicle $vehicle;

    public string $filter = 'all';

    public string $sort = 'helpful';

    public ?int $totalReviews = null;

    public ?float $avgRating = null;

    protected $queryString = ['filter' => ['except' => 'all'], 'sort' => ['except' => 'helpful']];

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->totalReviews = $vehicle->reviews()->where('is_approved', true)->count();
        $this->avgRating = $vehicle->avg_rating;
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }

    public function markHelpful(int $reviewId): void
    {
        $review = VehicleReview::find($reviewId);

        if ($review) {
            $review->increment('helpful_count');
        }
    }

    public function render()
    {
        $query = VehicleReview::with('customer')
            ->where('vehicle_id', $this->vehicle->id)
            ->where('is_approved', true);

        switch ($this->filter) {
            case '5stars':
                $query->where('rating', 5);
                break;
            case 'photos':
                $query->whereNotNull('photos');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                break;
        }

        switch ($this->sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'helpful':
            default:
                $query->orderBy('helpful_count', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        $reviews = $query->paginate(5);

        $ratingsDistribution = $this->getRatingsDistribution();
        $verifiedCount = VehicleReview::where('vehicle_id', $this->vehicle->id)
            ->where('is_approved', true)
            ->where('is_verified_booking', true)
            ->count();

        return view('livewire.frontend.review-list', [
            'reviews' => $reviews,
            'ratingsDistribution' => $ratingsDistribution,
            'verifiedCount' => $verifiedCount,
        ]);
    }

    private function getRatingsDistribution(): array
    {
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        $reviews = VehicleReview::where('vehicle_id', $this->vehicle->id)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        foreach ($reviews as $rating => $count) {
            $distribution[(int) $rating] = $count;
        }

        return $distribution;
    }
}
