<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MetaTags extends Component
{
    /**
     * El título de la página.
     *
     * @var string
     */
    public $title;

    /**
     * La descripción de la página para meta tags.
     *
     * @var string
     */
    public $description;

    /**
     * La URL de la imagen para compartir en redes sociales.
     *
     * @var string
     */
    public $image;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $title
     * @param  string|null  $description
     * @param  string|null  $image
     * @return void
     */
    public function __construct($title = null, $description = null, $image = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.meta-tags');
    }
}