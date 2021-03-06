<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Relative extends JsonResource
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
            'birthplace' => $this->birthplace,
            'rg' => $this->rg,
            'rg_issuer' => $this->rg_issuer,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'profession' => $this->profession,
            'note' => $this->note,
            'assisted_id' => $this->assisted_id,
            'addresses' => json_decode($this->addresses, true)
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'version' => '1.0.0'
        ];
    }
}
