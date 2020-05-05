<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Center;
use App\Models\City;
use App\Models\Student;

class StudentSearch
{
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        /*
        :TODO:
        Search options: teacher_id, level_id
         */

        if (isset($args['city_id'])) {
            $city_model = new City;
            $students = $city_model->fetch($args['city_id'])->students($args)->get();

        } else if (isset($args['center_id'])) {
            $center_model = new Center;
            $students = $center_model->fetch($args['center_id'])->students($args)->get();
        }
        

        return $students;
    }
}
