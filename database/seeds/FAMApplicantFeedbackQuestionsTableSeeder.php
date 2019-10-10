<?php

use Illuminate\Database\Seeder;

class FAMApplicantFeedbackQuestionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('FAM_ApplicantFeedbackQuestions')->delete();
        
        \DB::table('FAM_ApplicantFeedbackQuestions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'question' => 'Do you feel applicant would make an excellent Fellow/Mentor?',
                'type' => 'text',
                'target' => 'all',
                'description' => 'If so, please use the box below to tell us why. The more specifics you can share the better.',
                'comment' => '',
                'status' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'question' => 'How open is the applicant to learning?',
                'type' => 'scale',
                'target' => 'fellow',
                'description' => 'If you have given them critical feedback, how did they respond to it?',
                'comment' => 'Not open at all - Gets defensive when given feedback and is negative towards improving, Is neutral towards feedback -  If given feedback, engages with it to some extent, Very open - Welcomes or proactively seeks feedback and is excited about improving',
                'status' => '1',
            ),
            2 => 
            array (
                'id' => 3,
                'question' => 'How easy are they to work with?',
                'type' => 'scale',
                'target' => 'fellow',
                'description' => 'Are they great with other volunteers and team members or do they regularly have interactional issues that make you or others nervous around them? ',
            'comment' => 'Not at all easy - They worked in isolation from the team and other peers in the shelter home found it difficult to work with him/her, Somewhat Easy - Worked with others within the shelter when needed but didn\'t take additional ownership to build bonds, Very easy - Collaborated with other peers in the shelter and was easy to work with; had good relations with the shelter team and beyond (e.g. Fellows Mentors Directors etc)',
                'status' => '1',
            ),
            3 => 
            array (
                'id' => 4,
                'question' => 'How would you rate this volunteer on their proactiveness, consistency and participation?',
                'type' => 'scale',
                'target' => 'fellow',
                'description' => NULL,
                'comment' => '',
                'status' => '0',
            ),
            4 => 
            array (
                'id' => 5,
                'question' => 'How would you rate this volunteer on their proactiveness, consistency and participation?',
                'type' => 'scale',
                'target' => 'fellow',
                'description' => 'Do they go above and beyond or do they do the bare minimum and regularly go missing on ground without any prior information?',
                'comment' => 'They were lax in their work and did not meet commitments taken up.,They met all timelines, commitments and other basic expectations in terms of participation but level of participation and engagement was not beyond what was expected.,They took ownership beyond the basic expectations of their role and participation and engagement was beyond what was expected. ',
                'status' => '1',
            ),
            5 => 
            array (
                'id' => 6,
                'question' => 'Has the applicant ever reacted in a negative manner towards children, other volunteers or authorities in the Shelter?',
                'type' => 'radio',
                'target' => 'fellow',
                'description' => 'Negative manner= shouted or spoke rudely, used foul language, hit a child, etc.',
                'comment' => '',
                'status' => '0',
            ),
            6 => 
            array (
                'id' => 7,
                'question' => 'Is there any other information which you are just dying to provide us with about this candidate on why they will/will not be suited for the Fellowship role?',
                'type' => 'text',
                'target' => 'fellow',
                'description' => '',
                'comment' => '',
                'status' => '0',
            ),
            7 => 
            array (
                'id' => 8,
                'question' => 'Have you faced any challenges/noticed any concerning behaviours which you think would make applicant unsuitable for a Fellowship/Mentorship profile?',
                'type' => 'text',
                'target' => 'all',
            'description' => 'If so, please use the box below to tell us why. The more specifics you can share the better. <br><br>  With great power comes great responsibility... :). Please be thoughtful and gentle with this feedback as far as you can. Remember that we\'re all in together, and everyone makes mistakes sometimes. If there\'s anything you feel needs additional escalation, please contact our Human Capital team directly at <strong>humancapital@makeadiff.in</strong>.',
            'comment' => NULL,
            'status' => '1',
        ),
        8 => 
        array (
            'id' => 9,
            'question' => 'How is their interaction with, and attitude towards, children?',
            'type' => 'scale',
            'target' => 'fellow',
            'description' => 'Are they kind and amazing with children, or have you known them to react in a negative or aggressive manner towards or around children?',
            'comment' => NULL,
            'status' => '1',
        ),
        9 => 
        array (
            'id' => 10,
            'question' => 'How is their interaction with, and attitude towards, shelter authorities?',
            'type' => 'scale',
            'target' => 'fellow',
            'description' => 'Are they ambassadors for MAD, building and maintaining good relationships with shelter authorities, or have you known them to react in a negative manner towards them?',
            'comment' => NULL,
            'status' => '1',
        ),
        10 => 
        array (
            'id' => 11,
            'question' => 'Overall do you recommend them for Fellowship/Mentorship?',
            'type' => 'radio',
            'target' => 'fellow',
            'description' => NULL,
            'comment' => NULL,
            'status' => '1',
        ),
        11 => 
        array (
            'id' => 12,
            'question' => 'Any final comments you would like to add with respect to the feedback or recommendation you\'ve provided?',
            'type' => 'text',
            'target' => 'fellow',
            'description' => NULL,
            'comment' => NULL,
            'status' => '1',
        ),
    ));
        
        
    }
}