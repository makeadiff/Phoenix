<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Batch;
use App\Models\City;
use App\Models\User;
use App\Models\Classes;

class ClassSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'class:schedule {project_id=0} {city_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule classes 2 weeks into the future.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();

        $all_project_ids = [];
        if ($arguments['project_id']) {
            $all_project_ids = [ $arguments['project_id'] ];
        } else {
            $all_project_ids = Project::getAll()->pluck('id')->toArray();
        }

        $all_city_ids = [];
        if ($arguments['city_id']) {
            $all_city_ids = [ $arguments['city_id'] ];
        } else {
            $all_city_ids = City::getAll()->pluck('id')->toArray();
        }

        $year = 2019;
        $year_end_time = ($year + 1) . '-03-31 23:59:59';

        $batch_model = new Batch;
        $user_model = new User;
        $class_model = new Classes;

        foreach ($all_project_ids as $project_id) {
            foreach ($all_city_ids as $city_id) {
                $all_batches = $batch_model->search(['project_id' => $project_id, 'city_id' => $city_id]);
                print "Creating class for Project [$project_id] in City [$city_id] : " . count($all_batches) . " batches...\n";

                for ($week = 0; $week < 2; $week++) {
                    foreach ($all_batches as $batch) {
                        $all_teachers = $user_model->search(['batch_id' => $batch->id]);
                        print "  Teachers in batch [{$batch->id}] : " . count($all_teachers) . "\n";
                        list($hour, $min, $secs) = explode(":", $batch->class_time);

                        // This is how we find the next sunday, monday(whatever is in the $batch->day).
                        $date_interval = intval($batch->day) - date('w');
                        if ($date_interval <= 0) {
                            $date_interval += 7;
                        }
                        $day = date('d') + $date_interval;

                        $day = $day + ($week * 7); // We have to do this for two weeks. So in the first iteration, this will be 0 and in next it will be 7.

                        $time = mktime($hour, $min, $secs, date('m'), $day, date("Y"));
                        $date = date("Y-m-d H:i:s", $time);

                        if ($date >= $year_end_time) {
                            continue;
                        } // If the classes fall on the next year, don't make them.

                        foreach ($all_teachers as $teacher) {
                            // if($teacher->id != 83172) continue; // :DEBUG: Use this to localize the issue. I would recommend keeping this commented. You'll need it a lot.

                            // Make sure its not already inserted.
                            $class_exists = $class_model->search(['teacher_id' => $teacher->id, 'class_on' => $date, 'batch_id' => $batch->id]);
                            if (! count($class_exists)) {
                                print "    Inserting Class by [{$teacher->id}] at $date\n";

                                $userbatch = app('db')->table('UserBatch')->where('batch_id', $batch->id)->where('user_id', $teacher->id)->first(); // Find the level_id using the user_id and batch_id
                                $class_model->add([
                                    'batch_id'      => $batch->id,
                                    'level_id'      => $userbatch->level_id,
                                    'teacher_id'    => $teacher->id,
                                    'project_id'    => $batch->project_id,
                                    'substitute_id' => 0,
                                    'class_on'      => $date,
                                    'status'        => 'projected'
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
