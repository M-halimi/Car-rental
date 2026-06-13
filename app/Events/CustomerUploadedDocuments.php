<?php

namespace App\Events;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class CustomerUploadedDocuments
{
    use Dispatchable;

    public function __construct(
        public Customer $customer,
        public User $user,
    ) {}
}
