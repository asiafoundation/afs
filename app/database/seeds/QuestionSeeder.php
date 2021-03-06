<?php

class QuestionSeeder extends Seeder {

  public function run()
  {
    $questions = array(
      array("7","Siapakah Calon Presiden Pilihan Anda","1","1"),
      array("1","Siapakah Calon Wakil Presiden Pilihan Anda","1","0"),
      array("7","Siapakah Ketua MPR Pilihan Anda","1","0"),
    );
      // array("0","Siapakah Calon Presiden Pilihan Anda","1","1","1"),
      // array("0","Siapakah Calon Presiden Pilihan Anda","1","2","0"),
      // array("1","Siapakah Calon Wakil Presiden Pilihan Anda","1","1","0"),
      // array("1","Siapakah Calon Wakil Presiden Pilihan Anda","1","2","0"),
    Question::truncate();

    foreach ($questions as $key => $question) {
      Question::create(
        array(
          "code_id" => $question[0],
          "question" => $question[1],
          "question_category_id" => $question[2],
          "is_default" => $question[3],
        )
      );
    }
  }
}