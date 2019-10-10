<?php

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('Permission')->delete();
        
        \DB::table('Permission')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'change_city',
                'value' => '',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'change_project',
                'value' => '',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'classes_batch_view',
                'value' => '',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'classes_index',
                'value' => '',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'user_add',
                'value' => '',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'user_edit',
                'value' => '',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'user_delete',
                'value' => '',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'user_group_edit',
                'value' => '',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'user_group_add',
                'value' => '',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'user_group_delete',
                'value' => '',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'kids_add',
                'value' => '',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'kids_edit',
                'value' => '',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'kids_delete',
                'value' => '',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'exam_details_add',
                'value' => '',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'exam_mark_add',
                'value' => '',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'project_add',
                'value' => '',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'project_edit',
                'value' => '',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'project_delete',
                'value' => '',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'center_add',
                'value' => '',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'center_edit',
                'value' => '',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'center_delete',
                'value' => '',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'city_create',
                'value' => '',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'center_index',
                'value' => '',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'exam_add',
                'value' => '',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'exam_view',
                'value' => '',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'exam_index',
                'value' => '',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'exam_marks_index',
                'value' => '',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'exam_delete',
                'value' => '',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'level_index',
                'value' => '',
            ),
            29 => 
            array (
                'id' => 59,
                'name' => 'batch_create',
                'value' => '',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'level_edit',
                'value' => '',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'permission_index',
                'value' => '',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'permission_add',
                'value' => '',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'permission_edit',
                'value' => '',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'permission_delete',
                'value' => '',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'user_group_index',
                'value' => '',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'user_group_view',
                'value' => '',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'project_index',
                'value' => '',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'user_index',
                'value' => '',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'user_export',
                'value' => '',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'report_index',
                'value' => '',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'report_view',
                'value' => '',
            ),
            42 => 
            array (
                'id' => 44,
                'name' => 'classes_madsheet',
                'value' => '',
            ),
            43 => 
            array (
                'id' => 45,
                'name' => 'classes_mark_attendence',
                'value' => '',
            ),
            44 => 
            array (
                'id' => 46,
                'name' => 'kids_index',
                'value' => '',
            ),
            45 => 
            array (
                'id' => 47,
                'name' => 'batch_index',
                'value' => '',
            ),
            46 => 
            array (
                'id' => 49,
                'name' => 'batch_add_volunteers',
                'value' => '',
            ),
            47 => 
            array (
                'id' => 50,
                'name' => 'batch_edit',
                'value' => '',
            ),
            48 => 
            array (
                'id' => 51,
                'name' => 'level_delete',
                'value' => '',
            ),
            49 => 
            array (
                'id' => 52,
                'name' => 'batch_delete',
                'value' => '',
            ),
            50 => 
            array (
                'id' => 54,
                'name' => 'city_edit',
                'value' => '',
            ),
            51 => 
            array (
                'id' => 55,
                'name' => 'city_index',
                'value' => '',
            ),
            52 => 
            array (
                'id' => 56,
                'name' => 'class_edit_class',
                'value' => '',
            ),
            53 => 
            array (
                'id' => 58,
                'name' => 'level_create',
                'value' => '',
            ),
            54 => 
            array (
                'id' => 60,
                'name' => 'setting_index',
                'value' => '',
            ),
            55 => 
            array (
                'id' => 61,
                'name' => 'setting_create',
                'value' => '',
            ),
            56 => 
            array (
                'id' => 62,
                'name' => 'setting_edit',
                'value' => '',
            ),
            57 => 
            array (
                'id' => 63,
                'name' => 'user_view',
                'value' => '',
            ),
            58 => 
            array (
                'id' => 64,
                'name' => 'books_add',
                'value' => '',
            ),
            59 => 
            array (
                'id' => 65,
                'name' => 'books_edit',
                'value' => '',
            ),
            60 => 
            array (
                'id' => 66,
                'name' => 'event_index',
                'value' => '',
            ),
            61 => 
            array (
                'id' => 67,
                'name' => 'event_add',
                'value' => '',
            ),
            62 => 
            array (
                'id' => 68,
                'name' => 'event_mark_attendance',
                'value' => '',
            ),
            63 => 
            array (
                'id' => 69,
                'name' => 'event_edit',
                'value' => '',
            ),
            64 => 
            array (
                'id' => 70,
                'name' => 'event_delete',
                'value' => '',
            ),
            65 => 
            array (
                'id' => 71,
                'name' => 'books_index',
                'value' => '',
            ),
            66 => 
            array (
                'id' => 72,
                'name' => 'chapters_index',
                'value' => '',
            ),
            67 => 
            array (
                'id' => 73,
                'name' => 'user_credithistory',
                'value' => '',
            ),
            68 => 
            array (
                'id' => 74,
                'name' => 'permissions_index',
                'value' => '',
            ),
            69 => 
            array (
                'id' => 75,
                'name' => 'chapters_add',
                'value' => '',
            ),
            70 => 
            array (
                'id' => 76,
                'name' => 'chapters_edit',
                'value' => '',
            ),
            71 => 
            array (
                'id' => 77,
                'name' => 'chapters_delete',
                'value' => '',
            ),
            72 => 
            array (
                'id' => 78,
                'name' => 'books_delete',
                'value' => '',
            ),
            73 => 
            array (
                'id' => 79,
                'name' => 'see_applicants',
                'value' => '',
            ),
            74 => 
            array (
                'id' => 80,
                'name' => 'task_index',
                'value' => '',
            ),
            75 => 
            array (
                'id' => 81,
                'name' => 'admincredit_index',
                'value' => '',
            ),
            76 => 
            array (
                'id' => 82,
                'name' => 'user_bulk_email',
                'value' => '',
            ),
            77 => 
            array (
                'id' => 83,
                'name' => 'user_bulk_sms',
                'value' => '',
            ),
            78 => 
            array (
                'id' => 84,
                'name' => 'debug',
                'value' => '',
            ),
            79 => 
            array (
                'id' => 85,
                'name' => 'task_add',
                'value' => '',
            ),
            80 => 
            array (
                'id' => 86,
                'name' => 'task_delete',
                'value' => '',
            ),
            81 => 
            array (
                'id' => 87,
                'name' => 'task_edit',
                'value' => '',
            ),
            82 => 
            array (
                'id' => 88,
                'name' => 'admincredit_add_credit',
                'value' => '',
            ),
            83 => 
            array (
                'id' => 89,
                'name' => 'admincredit_add_task',
                'value' => '',
            ),
            84 => 
            array (
                'id' => 90,
                'name' => 'admincredit_index_all',
                'value' => '',
            ),
            85 => 
            array (
                'id' => 91,
                'name' => 'classes_progress_report',
                'value' => '',
            ),
            86 => 
            array (
                'id' => 92,
                'name' => 'exam_add_event',
                'value' => '',
            ),
            87 => 
            array (
                'id' => 93,
                'name' => 'exam_add_marks',
                'value' => '',
            ),
            88 => 
            array (
                'id' => 94,
                'name' => 'exam_save_marks',
                'value' => '',
            ),
            89 => 
            array (
                'id' => 95,
                'name' => 'exam_view_exam_events',
                'value' => '',
            ),
            90 => 
            array (
                'id' => 96,
                'name' => 'exam_view_scores',
                'value' => '',
            ),
            91 => 
            array (
                'id' => 97,
                'name' => 'exam_delete_event',
                'value' => '',
            ),
            92 => 
            array (
                'id' => 98,
                'name' => 'national_dashboard',
                'value' => '',
            ),
            93 => 
            array (
                'id' => 99,
                'name' => 'comps_view',
                'value' => '',
            ),
            94 => 
            array (
                'id' => 100,
                'name' => 'monthly_review',
                'value' => '',
            ),
            95 => 
            array (
                'id' => 101,
                'name' => 'monthly_review_edit',
                'value' => '',
            ),
            96 => 
            array (
                'id' => 102,
                'name' => 'placement_index',
                'value' => '',
            ),
            97 => 
            array (
                'id' => 103,
                'name' => 'user_bulk_edit',
                'value' => '',
            ),
            98 => 
            array (
                'id' => 104,
                'name' => 'review_milestone_create',
                'value' => '',
            ),
            99 => 
            array (
                'id' => 105,
                'name' => 'review_fellows',
                'value' => '',
            ),
            100 => 
            array (
                'id' => 106,
                'name' => 'milestone_list',
                'value' => '',
            ),
            101 => 
            array (
                'id' => 107,
                'name' => 'milestone_create',
                'value' => '',
            ),
            102 => 
            array (
                'id' => 108,
                'name' => 'milestone_my',
                'value' => '',
            ),
            103 => 
            array (
                'id' => 109,
                'name' => 'milestone_do',
                'value' => '',
            ),
            104 => 
            array (
                'id' => 110,
                'name' => 'hr_requirement',
                'value' => '',
            ),
            105 => 
            array (
                'id' => 111,
                'name' => 'review_milestone_edit',
                'value' => '',
            ),
            106 => 
            array (
                'id' => 112,
                'name' => 'okr_my',
                'value' => '',
            ),
            107 => 
            array (
                'id' => 113,
                'name' => 'review_data_my',
                'value' => '',
            ),
            108 => 
            array (
                'id' => 114,
                'name' => 'parameter_calculate',
                'value' => '',
            ),
            109 => 
            array (
                'id' => 115,
                'name' => 'hr_requirement_national',
                'value' => '',
            ),
            110 => 
            array (
                'id' => 116,
                'name' => 'review_select_person',
                'value' => '',
            ),
            111 => 
            array (
                'id' => 117,
                'name' => 'user_edit_bank_details',
                'value' => '',
            ),
            112 => 
            array (
                'id' => 118,
                'name' => 'reimbursement',
                'value' => '',
            ),
            113 => 
            array (
                'id' => 119,
                'name' => 'event_budget_salesforce',
                'value' => '',
            ),
            114 => 
            array (
                'id' => 120,
                'name' => 'pr_requirement',
                'value' => '',
            ),
            115 => 
            array (
                'id' => 121,
                'name' => 'pr_content_submission',
                'value' => '',
            ),
            116 => 
            array (
                'id' => 122,
                'name' => 'target_setting',
                'value' => '',
            ),
            117 => 
            array (
                'id' => 123,
                'name' => 'happiness_index',
                'value' => '',
            ),
            118 => 
            array (
                'id' => 124,
                'name' => 'milestone_aggregator',
                'value' => '',
            ),
            119 => 
            array (
                'id' => 125,
                'name' => 'happiness_index_aggregator',
                'value' => '',
            ),
            120 => 
            array (
                'id' => 126,
                'name' => 'review_aggregator',
                'value' => '',
            ),
            121 => 
            array (
                'id' => 127,
                'name' => 'classes_assign_students',
                'value' => '',
            ),
            122 => 
            array (
                'id' => 128,
                'name' => 'classes_assign',
                'value' => '',
            ),
            123 => 
            array (
                'id' => 129,
                'name' => 'batch_level_assignment',
                'value' => '',
            ),
            124 => 
            array (
                'id' => 130,
                'name' => 'shelter_authority_details',
                'value' => '',
            ),
            125 => 
            array (
                'id' => 131,
                'name' => 'shelter_comments',
                'value' => '',
            ),
            126 => 
            array (
                'id' => 132,
                'name' => 'kids_show_deleted',
                'value' => '',
            ),
        ));
        
        
    }
}