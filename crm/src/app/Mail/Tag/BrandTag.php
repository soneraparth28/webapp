<?php


namespace App\Mail\Tag;


class BrandTag extends Tag
{
    protected $brand;

    public function __construct($brand, $notifier = null, $receiver = null)
    {
        $this->brand = $brand;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = route('brands.lists');
    }
    function notification()
    {
        return array_merge([
            '{name}' => $this->brand->name,
        ], $this->common());
    }
}
