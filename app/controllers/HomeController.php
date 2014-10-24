<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		// Get Default Question
		$default_questions = Question::DefaultQuestion(Input::get());
		$default_question = reset($default_questions);

		// Get catefory and question list
		$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
		$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

		$data = array(
			"survey" => Survey::first(),
			"filters" => Code::getFilter(),
			"cycles" => Cycle::select('id','name')->get(),
			"question_categories" => $split_data['question_categories'],
			"question_lists" => $split_data['question_lists'],
			"default_question" => $default_question,
			"question" => $default_questions,
			"regions" => QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions),
		);

		return View::make('home.index', $data);
	}

	public function filterSelect()
	{
		if(Request::ajax()){
			switch (Input::get('SelectedFilter')) {
				case 'cycle':
				
					$default_questions = Question::DefaultQuestion(Input::get());
					$load_filter = array("question" => $default_questions);
					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				case 'area':
					$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
					$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

					$filter_category = (string)View::make('home.filter_category',$split_data)->render();
					$filter_question = (string)View::make('home.filter_question',$split_data)->render();

					$split_data = $filter_category.";".$filter_question;

					$return = count($question_categories_query) > 0 ? $split_data : 0;

					return $return;
					break;

				case 'survey':
					$default_questions = Question::LoadQuestion(Input::get());
					$default_question = reset($default_questions);

					$load_filter = array();
					$load_filter = array(
						"survey" => Survey::first(),
						"default_question" => $default_question,
						"question" => $default_questions,
						"regions" => QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions),
					);

					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				case 'filters':
					$default_questions = Question::FilterQuestion(Input::get());;
					$load_filter = array("question" => $default_questions);
					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				case 'compare_cycle':
					$default_questions = Question::CompareCycle(Input::get());

					$return = count($default_questions) > 0 ? $default_questions : 0;

					return $return;
					break;

				case 'next_question':
					$default_questions = Question::NextQuestion(Input::get());
					$default_question = reset($default_questions);
					$load_filter = array(
						"survey" => Survey::first(),
						"default_question" => $default_question,
						"question" => $default_questions,
						"regions" => QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions),
					);

					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				case 'survey_area_dynamic':
					$default_questions = Question::LoadQuestion(Input::get());
					$default_question = reset($default_questions);

					$load_filter = array(
						"default_question" => $default_question,
						"question" => $default_questions,
					);

					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				case 'detail_chart':
					$default_questions = Category::DetailParticipant(Input::get());
					$default_question = reset($default_questions);

					$load_filter = array(
						"default_question" => $default_question,
						"question" => $default_questions,
					);

					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				case 'compare_all_cycle':
					$default_questions = Question::CompareCycle(Input::get());

					$return = count($default_questions) > 0 ? $default_questions : 0;

					return $return;
					break;

				default:
					return 0;
					break;
			}
		}

		return 0;
	}
}