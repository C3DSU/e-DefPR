<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Assisted extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'birth_date' => $this->birth_date,
            'birth_place' => $this->birth_place,
            'rg' => $this->rg,
            'rg_issuer' => $this->rg_issuer,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'profession' => $this->profession,
            //'counter_part' => $this->counter_part,
            'note' => $this->note,
            'birth_place' => json_decode($this->birth_place, true),
            'addresses' => json_decode($this->addresses, true)
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request) {
        return [
            'version' => '1.0.0'
        ];
    }
}
