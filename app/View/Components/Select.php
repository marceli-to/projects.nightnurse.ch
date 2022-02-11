<?php
namespace App\View\Components;
use Illuminate\View\Component;

class Select extends Component
{
  /**
   * Name
   *
   * @var string
   */
  public $name;

  /**
   * Label
   *
   * @var string
   */
  public $label;

  /**
   * Options
   *
   * @var array
   */
  public $options;

  /**
   * Required
   *
   * @var string
   */
  public $required;

  /**
   * Css
   *
   * @var string
   */
  public $css;

  /**
   * Create a new component instance.
   *
   * @param $name
   * @param $label
   * @param $options
   * @param $required
   * @param $css
   * 
   * @return void
   */
  public function __construct($name, $label, $options = [], $required = true, $css = NULL)
  {
    $this->name = $name;
    $this->label = $label;
    $this->options = $options;
    $this->required = $required;
    $this->css = $css;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    return view('components.select');
  }
}
