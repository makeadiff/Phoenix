<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Classes;
use App\Models\Project;

class classSearch
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // This is a better way - but doesn't work.
        // $class_model = new Classes;
        // $classes = $class_model->search($args)->get();

        // Use the project model to connect with the class because I can't figure out how to do it directly. Tried posting the question on stack overflow as well - https://stackoverflow.com/questions/56713311/implementing-search-funtionality-in-laravel-lighthouse-graphql-api
        $project_model = new Project;

        $project_id = 1;
        if(isset($args['project_id'])) $project_id = $args['project_id'];
        $classes = $project_model->find($project_id)->classes($args)->get();

        return $classes;
    }

    public function teacher_id($builder, int $value)
    {
        return $builder->where('teacher_id', $value);
    }
    public function status($builder, string $value)
    {
        return $builder->where('status', $value);
    }
}
