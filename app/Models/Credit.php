<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Parameter;
use App\Models\User;

final class Credit extends Common
{
    protected $table = 'Credit';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    protected $fillable = ['user_id', 'parameter_id', 'change', 'current_credit', 'item', 'item_id', 'comment', 'added_by_user_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function parameter()
    {
        return $this->belongsTo('App\Models\Parameter', 'parameter_id');
    }

    public static function search($data)
    {
        $q = app('db')->table('Credit AS C');

        $q->select('C.id', 'C.user_id', 'C.parameter_id', 'C.change', 'C.current_credit', 'C.item', 'C.item_id', 
                    'C.comment', 'C.added_by_user_id', 'C.added_on', 'P.name', 'P.vertical_id');
        $q->join('Parameter AS P', 'C.parameter_id', '=', 'P.id');
        
        if (!empty($data['user_id'])) {
            $q->where('C.user_id', $data['user_id']);
        }
        if (!empty($data['item_id'])) {
            $q->where('C.item_id', $data['item_id']);
        }
        if (!empty($data['item'])) {
            $q->where('C.item', $data['item']);
        }
        if (!empty($data['parameter_id'])) {
            $q->where('C.parameter_id', $data['parameter_id']);
        }
        if (!empty($data['name'])) {
            $q->where('P.name', $data['name']);
        }
        if (!empty($data['vertical_id']) and $data['vertical_id']) {
            $q->where('P.vertical_id', $data['vertical_id']);
        }

        $q->orderBy('added_on');

        // dd($q->toSql(), $q->getBindings());
        $credits = $q->get();
        
        return $credits;
    }
 
    public static function creditHistory($user_id, $options = [])
    {
        $options['user_id'] = $user_id;
        return self::search($options);
    }

    public function assign($user_id, $parameter_id, $options = [])
    {
        $options_template = [
            'added_on'  => date('Y-m-d H:i:s'), 
            'added_by_user_id' => 0, 
            'item'      => null, 
            'item_id'   => null,
            'revert'    => false
        ];
        $options = array_merge($options_template, $options);

        $user_model = new User;
        $user = $user_model->fetch($user_id);
        if(!$user) {
            $this->error("Can't find any user with the given ID($user_id)");
            return false;
        }

        $paramater_model = new Parameter;
        $para = $paramater_model->fetch($parameter_id);
        if(!$para) {
            $this->error("Given Paramater ID($parameter_id) is not valid.");
            return false;
        }

        if($options['revert']) {
            return $this->unassign($user_id, $parameter_id, $options['item'], $options['item_id']);
        }

        $current_credit = floatval($user->credit) + floatval($para->credit);
        $data = array_merge($options, [
            'user_id'        => $user_id,
            'parameter_id'   => $parameter_id,
            'change'         => $para->credit,
            'current_credit' => $current_credit,
        ]);

        // Update User's credit as well.
        $user->edit(['credit' => $current_credit], $user_id);

        return $this->create($data);
    }

    public function unassign($user_id, $parameter_id, $item, $item_id)
    {
        $credit = $this->search([
            'user_id'       => $user_id, 
            'parameter_id'  => $parameter_id, 
            'item'          => $item, 
            'item_id'       => $item_id
        ]);

        if(!count($credit)) { // No previous data.
            return 0;
        } elseif(count($credit) > 1) {
            $this->errors[] = "Multiple credit rows are matching your search query";
            // return false; 
        }
        $cre = $credit->last();

        return $this->unassignById($cre->id);
    }

    public function unassignById($credit_id)
    {
        $credit = $this->fetch($credit_id);
        if(!$credit) return false;

        $user_model = new User;
        $user = $user_model->find($credit->user_id);
        $user->credit = floatval($user->credit) - floatval($credit->change); // Revert credit change.
        $user->save();

        return $this->find($credit_id)->delete();
    }

// recalculateHistory($user_id)
    // Do this in a more optimized way. Don't use awardCredit function.
}
