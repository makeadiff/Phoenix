<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(CityTableSeeder::class);
        $this->call(EventTypeTableSeeder::class);
        $this->call(FAMApplicantFeedbackQuestionsTableSeeder::class);
        $this->call(FAMParameterTableSeeder::class);
        $this->call(FAMParameterCategoryTableSeeder::class);
        $this->call(FAMStageTableSeeder::class);
        $this->call(GroupTableSeeder::class);
        $this->call(GroupPermissionTableSeeder::class);
        $this->call(MediumTableSeeder::class);
        $this->call(ProjectTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(SubjectTableSeeder::class);
        $this->call(VerticalTableSeeder::class);
        $this->call(CenterTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(BatchTableSeeder::class);
        $this->call(LevelTableSeeder::class);
        $this->call(BatchLevelTableSeeder::class);
        $this->call(UserBatchTableSeeder::class);
        $this->call(UserGroupTableSeeder::class);
        $this->call(StudentTableSeeder::class);
        $this->call(StudentLevelTableSeeder::class);
    }
}
