<?php
namespace App\Models;

trait Common
{
    public $errors = [];

    public $year;
    public $year_start_date;
    public $year_start_time;
    protected $item_id = 0;
    protected $item = 0;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Current year
        $this_month = intval(date('m'));
        $months = [];
        $start_month = 7; // July, made this change for 2020 year. It was a wierd year.
        $start_year = date('Y');
        if ($this_month < $start_month) {
            $start_year = date('Y')-1;
        }
        $this->year = $start_year;
        $this->year_start_date = $start_year . "-0{$start_month}-01";
        $this->year_start_time = $this->year_start_date . ' 00:00:00';
    }

    public function isTableJoined($q, $table) 
    {
        $found = false;
        if(!$q->joins) return $found;
        
        foreach($q->joins as $jn) {
            if($jn->table === $table) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    public function joinOnce(&$q, $table, $field_1, $condition, $field_2)
    {
        if(!$this->isTableJoined($q, $table)) {
            $q->join($table, $field_1, $condition, $field_2);
        }
        return $q;
    }

    public function fetch($id)
    {
        $this->item_id = $id;
        if (in_array('status', $this->fillable)) {
            $this->item = $this->where('status', '1')->find($id);
        } else {
            $this->item = $this->find($id);
        }

        return $this->item;
    }

    public function edit($data, $id = false)
    {
        $this->chain($id);

        foreach ($this->fillable as $key) {
            if (!isset($data[$key])) {
                continue;
            }

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
        $this->item->status = '0';
        $this->item->save();

        return $this->item;
    }

    public function error($text)
    {
        $this->errors[] = $text;
        return false;
    }

    /// This is necessary to make the methord chaining work. With this, you can do stuff like - $item->find(3)->remove();
    protected function chain($id)
    {
        if ($id) {
            $this->item_id = $id;
        }
        if (!$this->item_id and !empty($this->attributes['id'])) {
            $this->item_id = $this->attributes['id'];
        }
        if (!$this->item_id and !empty($this->attributes['item_id'])) {
            $this->item_id = $this->attributes['item_id'];
        }

        if (!$this->item) {
            $this->item = $this->find($this->item_id);
        }

        return $this->item_id;
    }
}
