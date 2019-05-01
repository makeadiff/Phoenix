<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    public $errors = [];

    public $year;
    protected $item_id = 0;
    protected $item = 0;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Current year
        $this_month = intval(date('m'));
        $months = [];
        $start_month = 6; // June
        $start_year = date('Y');
        if($this_month < $start_month) $start_year = date('Y')-1;
        $this->year = $start_year;
    }

    public function fetch($id)
    {
        $this->item_id = $id;
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
            $this->item_id = $id;
        }
        if(!$this->item_id and $this->attributes['id']) {
            $this->item_id = $this->attributes['id'];
        }
        if(!$this->item_id and $this->attributes['item_id']) {
            $this->item_id = $this->attributes['item_id'];
        }

        if(!$this->item) {
            $this->item = $this->find($this->item_id);
        }

        return $this->item_id;
    }
}
