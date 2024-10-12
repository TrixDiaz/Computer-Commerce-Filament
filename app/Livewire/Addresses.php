<?php

namespace App\Livewire;

use Livewire\Component;

class Addresses extends Component
{
    public $addresses;
    public $showForm = false;

    public $address_line_1;
    public $address_line_2;
    public $city;
    public $state;
    public $postal_code;
    public $country;

    public $showDeleteModal = false;
    public $addressToDelete;

    public function mount()
    {
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $this->addresses = auth()->user()->addresses;
    }

    public function showAddressForm()
    {
        $this->showForm = true;
    }

    public function addAddress()
    {
        $this->validate([
            'address_line_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postal_code' => 'required',
            'country' => 'required',
        ]);

        auth()->user()->addresses()->create([
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
        ]);

        $this->resetForm();
        $this->loadAddresses();
    }

    private function resetForm()
    {
        $this->address_line_1 = '';
        $this->address_line_2 = '';
        $this->city = '';
        $this->state = '';
        $this->postal_code = '';
        $this->country = '';
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.addresses');
    }

    public function confirmDeleteAddress($addressId)
    {
        $this->addressToDelete = $addressId;
        $this->showDeleteModal = true;
    }

    public function deleteAddress()
    {
        $address = auth()->user()->addresses()->find($this->addressToDelete);
        
        if ($address) {
            $address->delete();
            $this->loadAddresses();
        }

        $this->showDeleteModal = false;
        $this->addressToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->addressToDelete = null;
    }
}
