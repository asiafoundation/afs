<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$default_questions = Question::DefaultQuestion(Input::get());
		$default_question = reset($default_questions);

		$data = array(
			"survey" => Survey::find(1),
			"filters" => Code::getFilter(),
			"cycles" => Cycles::select('id','name')->get(),
			"question_categories" => QuestionCategory::select('id','name')->get(),
			"question_lists" => Question::select('id','question')->get(),
			"default_question" => $default_question,
			"question" => $default_questions,
			"regions" => Region::RegionColor(),
		);

    if(Request::ajax()){
      return View::make('home.survey_pemilu',$data);
    }
    else
    {
			return View::make('home.index', $data);
    }
	}

	public function filterSelect()
	{
		if(Request::ajax()){
			switch (Input::get('SelectedFilter')) {
				case 'area':
					$question_categories_query = Region::QuestionCategoryFilterRegion(Input::get());
					$split_data = Region::SplitQuestionsCategory($question_categories_query);

					$filter_category = (string)View::make('home.filter_category',$split_data)->render();
					$filter_question = (string)View::make('home.filter_question',$split_data)->render();

					$split_data = $filter_category.";".$filter_question;
					break;

				case 'survey':
					$default_questions = Question::LoadQuestion(Input::get());
					$default_question = reset($default_questions);

					$load_filter = array();
					$load_filter = array(
						"survey" => Survey::find(1),
						"default_question" => $default_question,
						"question" => $default_questions,
					);

					return View::make('home.survey_pemilu', $load_filter);
					break;

				default:
					# code...
					break;
			}
		}

		return $split_data;
	}
}