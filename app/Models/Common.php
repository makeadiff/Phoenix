<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    public $errors = array();

    public $year;
    protected $id = 0;
    protected $item = 0;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->year = 2017; // :TODO:
    }

    public function fetch($id)
    {
        $this->id = $id;
        $this->item = $this->where('status', '1')->find($id);

        return $this->item;
    }

    public function edit($data, $id = false)
    {
        $this->chain($id);

        foreach ($this->fillable as $key) {
            if(!isset($data[$key])) continue;

            $this->item->$key = $data[$key];
        }
        $this->item->save();

        return $this->item;
    }

    public function remove()
    {
        list($id) = func_get_args(); // We do this instead of putting the args in the parameter list to make sure overloading works.

        $this->chain($id);

        $this->item = $this->find($id);
        $this->item->status = 0;
        $this->item->save();

        return $this->item;
    }

    public function error($text) {
        $this->errors[] = $text;
        return false;
    }

    /// This is necessary to make the methord chaining work. With this, you can do stuff like - $item->find(3)->remove();
    protected function chain($id) {
        if($id) {
            $this->id = $id;
        }
        if(!$this->id and $this->attributes['id']) {
            $this->id = $this->attributes['id'];
        }

        if(!$this->item) {
            $this->item = $this->find($this->id);
        }

        return $this->id;
    }
}
