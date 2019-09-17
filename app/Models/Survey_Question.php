<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Survey_Question_Category;
use App\Models\Survey_Choice;
use Validator;

final class Survey_Question extends Common
{
    protected $table = 'Survey_Question';
    public $timestamps = false;
    protected $fillable = ['question', 'survey_question_category_id', 'survey_template_id', 'response_type', 'required', 'sort_order'];

    public function responses()
    {
        return $this->hasMany('App\Models\Survey_Response');
    }

    public function choices()
    {
        return $this->hasMany('App\Models\Survey_Choice')->where('Survey_Choice.status', '=', '1');
    }

    public function survey_template()
    {
        return $this->belongsTo('App\Models\Survey_Template', 'survey_template_id');
    }

    public function survey_question_category()
    {
        return $this->belongsTo('App\Models\Survey_Question_Category', 'survey_question_category_id');
    }

    public static function search($data)
    {
        $q = app('db')->table('Survey_Question');

        $q->select("id", "question", "description", "survey_question_category_id", 'response_type', 'required', 'sort_order', 'options', app('db')->raw("'question' AS type"));

        if (!isset($data['status'])) {
            $data['status'] = '1';
        }
        if ($data['status'] !== false) {
            $q->where('status', $data['status']);
        } // Setting status as '0' gets you even the deleted question
        
        if (isset($data['survey_template_id']) and $data['survey_template_id'] != 0) {
            $q->where('survey_template_id', $data['survey_template_id']);
        }
        if (isset($data['survey_question_category_id'])) {
            $q->where('survey_question_category_id', $data['survey_question_category_id']);
        }
        if (!empty($data['id'])) {
            $q->where('id', $data['id']);
        }
        if (!empty($data['question_id'])) {
            $q->where('id', $data['question_id']);
        }
        if (!empty($data['response_type'])) {
            $q->where('response_type', $data['response_type']);
        }
        if (!empty($data['required'])) {
            $q->where('required', $data['required']);
        }
        
        if (!empty($data['survey_id'])) {
            $survey = Survey::fetch($data['survey_id']);
            if ($survey) {
                $q->where("survey_template_id", $survey->survey_template_id);
            }
        }
        $q->orderby('sort_order');
        // dd($q->toSql(), $q->getBindings(), $data);

        $results = $q->get();

        foreach ($results as $index => $question) {
            if ($question->response_type == 'choice' or $question->response_type == 'multi-choice') {
                $results[$index]->choices = Survey_Choice::inQuestion($question->id);
            }
        }
        return $results;
    }

    /// Returns all the questions with categories as well. The questions/category linking and hirachy will be precerved. Use this by default.
    public function inCategorizedFormat($survey_template_id, $survey_id=0)
    {
        if (!$survey_template_id and $survey_id) {
            $survey = Survey::fetch($survey_id);
            $survey_template_id = $survey->survey_template_id;
        }

        if (!$survey_template_id) {
            return $this->error("Can't find Survey Template ID. Make sure its passed as an argument like this - $survey_question->inCategorizedFormat(3)");
        }

        $questions = Survey_Question::search(['survey_template_id' => $survey_template_id, 'survey_question_category_id' => 0]);

        $categories = Survey_Question_Category::inSurveyTemplate($survey_template_id);
        foreach ($categories as $category) {
            $category->type = 'category';
            $category->questions = Survey_Question::search(['survey_template_id' => $survey_template_id, 'survey_question_category_id' => $category->id]);

            $questions[] = $category;
        }

        return $questions;
    }

    public function addMany($data, $survey_template_id = 0)
    {
        $questions = [];
        $choice_model = new Survey_Choice;
        $sort_order  = 0;
        foreach ($data as $index => $fields) {
            if (empty($fields['survey_template_id']) and $survey_template_id) {
                $fields['survey_template_id'] = $survey_template_id;
            }

            // Validation...
            $validator = Validator::make($fields, [
                'question'              => 'required',
                'survey_template_id'    => 'required|integer|exists:Survey_Template,id',
                'response_type'         => 'required|in:text,choice,number,1-10,1-5,yes-no,date,datetime,file'
            ]);
            if ($validator->fails()) {
                $this->error($validator->errors());
            } else {
                $sort_order += 10;
                $fields['sort_order'] = $sort_order;

                if (!isset($fields['name'])) {
                    $fields['name'] = Survey_Question::questionName($fields['question']);
                }
                $last_question = json_decode(json_encode(Survey_Question::create($fields))); // I was not getting the ID without doing this. Because it was protected.
                $questions[] = $last_question;
                
                if ($fields['response_type'] == 'choice' and isset($fields['choices']) and is_array($fields['choices'])) {
                    $status = $choice_model->addMany($fields['choices'], $last_question->id);
                    if (!$status) {
                        $this->error($choice_model->errors);
                    }
                }
            }
        }

        return $questions;
    }

    private static function questionName($question)
    {
        $common_words = array("a","able","about","above","abroad","according","accordingly","across","actually","adj","after","afterwards","again","against","ago","ahead","ain't","all","allow","allows","almost","alone","along","alongside","already","also","although","always","am","amid","amidst","among","amongst","an","and","another","any","anybody","anyhow","anyone","anything","anyway","anyways","anywhere","apart","appear","appreciate","appropriate","are","aren't","around","as","a's","aside","ask","asking","associated","at","available","away","awfully","b","back","backward","backwards","be","became","because","become","becomes","becoming","been","before","beforehand","begin","behind","being","believe","below","beside","besides","best","better","between","beyond","both","brief","but","by","c","came","can","cannot","cant","can't","caption","cause","causes","certain","certainly","changes","clearly","c'mon","co","co.","com","come","comes","concerning","consequently","consider","considering","contain","containing","contains","corresponding","could","couldn't","course","c's","currently","d","dare","daren't","definitely","described","despite","did","didn't","different","directly","do","does","doesn't","doing","done","don't","down","downwards","during","e","each","edu","eg","eight","eighty","either","else","elsewhere","end","ending","enough","entirely","especially","et","etc","even","ever","evermore","every","everybody","everyone","everything","everywhere","ex","exactly","example","except","f","fairly","far","farther","few","fewer","fifth","first","five","followed","following","follows","for","forever","former","formerly","forth","forward","found","four","from","further","furthermore","g","get","gets","getting","given","gives","go","goes","going","gone","got","gotten","greetings","h","had","hadn't","half","happens","hardly","has","hasn't","have","haven't","having","he","he'd","he'll","hello","help","hence","her","here","hereafter","hereby","herein","here's","hereupon","hers","herself","he's","hi","him","himself","his","hither","hopefully","how","howbeit","however","hundred","i","i'd","ie","if","ignored","i'll","i'm","immediate","in","inasmuch","inc","inc.","indeed","indicate","indicated","indicates","inner","inside","insofar","instead","into","inward","is","isn't","it","it'd","it'll","its","it's","itself","i've","j","just","k","keep","keeps","kept","know","known","knows","l","last","lately","later","latter","latterly","least","less","lest","let","let's","like","liked","likely","likewise","little","look","looking","looks","low","lower","ltd","m","made","mainly","make","makes","many","may","maybe","mayn't","me","mean","meantime","meanwhile","merely","might","mightn't","mine","minus","miss","more","moreover","most","mostly","mr","mrs","much","must","mustn't","my","myself","n","name","namely","nd","near","nearly","necessary","need","needn't","needs","neither","never","neverf","neverless","nevertheless","new","next","nine","ninety","no","nobody","non","none","nonetheless","noone","no-one","nor","normally","not","nothing","notwithstanding","novel","now","nowhere","o","obviously","of","off","often","oh","ok","okay","old","on","once","one","ones","one's","only","onto","opposite","or","other","others","otherwise","ought","oughtn't","our","ours","ourselves","out","outside","over","overall","own","p","particular","particularly","past","per","perhaps","placed","please","plus","possible","presumably","probably","provided","provides","q","que","quite","qv","r","rather","rd","re","really","reasonably","recent","recently","regarding","regardless","regards","relatively","respectively","right","round","s","said","same","saw","say","saying","says","second","secondly","see","seeing","seem","seemed","seeming","seems","seen","self","selves","sensible","sent","serious","seriously","seven","several","shall","shan't","she","she'd","she'll","she's","should","shouldn't","since","six","so","some","somebody","someday","somehow","someone","something","sometime","sometimes","somewhat","somewhere","soon","sorry","specified","specify","specifying","still","sub","such","sup","sure","t","take","taken","taking","tell","tends","th","than","thank","thanks","thanx","that","that'll","thats","that's","that've","the","their","theirs","them","themselves","then","thence","there","thereafter","thereby","there'd","therefore","therein","there'll","there're","theres","there's","thereupon","there've","these","they","they'd","they'll","they're","they've","thing","things","think","third","thirty","this","thorough","thoroughly","those","though","three","through","throughout","thru","thus","till","to","together","too","took","toward","towards","tried","tries","truly","try","trying","t's","twice","two","u","un","under","underneath","undoing","unfortunately","unless","unlike","unlikely","until","unto","up","upon","upwards","us","use","used","useful","uses","using","usually","v","value","various","versus","very","via","viz","vs","w","want","wants","was","wasn't","way","we","we'd","welcome","well","we'll","went","were","we're","weren't","we've","what","whatever","what'll","what's","what've","when","whence","whenever","where","whereafter","whereas","whereby","wherein","where's","whereupon","wherever","whether","which","whichever","while","whilst","whither","who","who'd","whoever","whole","who'll","whom","whomever","who's","whose","why","will","willing","wish","with","within","without","wonder","won't","would","wouldn't","x","y","yes","yet","you","you'd","you'll","your","you're","yours","yourself","yourselves","you've","z","zero");
        return preg_replace('/\b('.implode('|', $common_words).')\b/', '', $question);
    }
}
