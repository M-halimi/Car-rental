<?php

namespace App\Livewire\Frontend;

use App\Models\Agency;
use Livewire\Component;

class TrustBadges extends Component
{
    public ?Agency $agency = null;

    public function mount(?int $agencyId = null): void
    {
        if ($agencyId) {
            $this->agency = Agency::find($agencyId);
        }
    }

    public function render()
    {
        return view('livewire.frontend.trust-badges', [
            'stats' => [
                ['value' => $this->agency?->rentals_completed_count ?? '500+', 'label' => __('frontend.rentals_completed') ?? 'Rentals Completed'],
                ['value' => $this->agency?->response_rate ?? '98%', 'label' => __('frontend.response_rate') ?? 'Response Rate'],
                ['value' => $this->agency?->avg_response_time ?? '5 min', 'label' => __('frontend.avg_response_time') ?? 'Avg Response Time'],
            ],
        ]);
    }
}
