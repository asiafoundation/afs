  <script type="text/javascript">
     function find_survey()
     {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"survey",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answers: FilterSelect.answers} )
        .done(function( data ) {
          if (data != false) {
            // Re declare object filter data 
            cycle_id = FilterSelect.cycle;

            FilterSelect.answers = [];
            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }

            // cycle list
            var cycle_list = "";
            var data_cycles = data.cycles;
            for (var key in data_cycles) {
              if (data_cycles.hasOwnProperty(key)) {
                cycle_list =cycle_list+'<li><a href="#" onclick="cycle_select('+data_cycles[key].id+')" id="'+data_cycles[key].id+'">'+data_cycles[key].name+'</a></li>'; 
              }
            }

            // Build chart
            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);
            var data_points_pie_data = data_points_pie(data.question);

            $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
            chartjs(color_set_data,data_points_data,data_points_pie_data);

            var cycle_text = $("#cycle_select_"+cycle_id).text();
            $("#cycle_list").html(cycle_list);

            $("#question-name").html(data.default_question.question);
            $("#select_cycle_label").html(cycle_text);
            $("#select_category_label").html(data.default_question.question_categories.slice(0,20)+" ...");
            $("#select_question_label").html(data.default_question.question.slice(0,40)+" ...");
            $(".chart-pagination").html('<li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="compare_cycle(0)">{{Lang::get('frontend.compare_this_survey')}}</a></li><li><a class="orange-bg" onclick="next_question(1)"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');

            // Re assign map
            dynamicRegions = data.regions;
            // Load New map
            geojson = L.geoJson(statesData, {
              style: styleDynamic,
              onEachFeature: onEachFeature,
            }).addTo(map);
            // Re assingn Filter data
            DefaultSelectAssign(FilterSelect);
          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
      return false;
     }

     function cycle_select(cycle_id)
     {
        // Re declare object filter data 
        FilterSelect.cycle = cycle_id;

        // Get cycles functions
        $.get( "filter-select", {SelectedFilter:"cycle",category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answers: FilterSelect.answers} )
          .done(function( data ) {
            if (data != false) {

              var cycle_text = $("#cycle_select_"+cycle_id).text();
              $("#select_cycle_label").html(cycle_text);

              FilterSelect.answers = [];
              for (var key in data.question) {
                if (data.question.hasOwnProperty(key)) {
                  FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
                }
              }

              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);
              var data_points_pie_data = data_points_pie(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data,data_points_pie_data);

              // Re assign map
              dynamicRegions = data.regions;
              // Load New map
              geojson = L.geoJson(statesData, {
                style: styleDynamic,
                onEachFeature: onEachFeature,
              }).addTo(map);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
        },"html");
     }

     function filter_option(category_id)
     {
        var option_filters = [];
        $(".selected_filter_option").each(function(){
          var data_value = $(this).attr("data-value");

          if(data_value % 1 === 0){
            option_filters += $(this).attr("data-value")+",";
          }
        });

        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"filters",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, option_filters: option_filters} )
          .done(function( data ) {
            if (data != false) {
              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);
              var data_points_pie_data = data_points_pie(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data,data_points_pie_data);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
     }

    function compare_cycle(move)
    {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"compare_cycle",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, FilterMove: move} )
        .done(function( data ) {
          if (data != false) {

            // Build chart
            $("#chart_canvas").html('<div class="col-md-12"><div id="compareChart" style="height: 345px; width: 100%;"></div></div>');

            var first_list = [];
            var end_list = [];
            var colorSet = [];
            var baseline_text = "";
            var endline_text = "";
            var question_text = "";

            for (i = 0; i < data.question.length; i++) {
              if (data.question[i].cycle_type == 0) {

                baseline_text = data.question[i].cycle;
                question_text = data.question[i].question;
                FilterSelect.question = data.question[i].id_question;

                first_list.push({ y: parseInt(data.question[i].amount), label: data.question[i].answer});

                colorSet.push(data.question[i].color);
              }
              if (data.question[i].cycle_type == 1) {
                endline_text = data.question[i].cycle;
                end_list.push({ y: parseInt(data.question[i].amount), label: data.question[i].answer});
              }
            }

            compare_chart(first_list,end_list, colorSet, baseline_text,endline_text);

            FilterSelect.answers = [];
            if (move == 0) {
              $('.chart-pagination').html('<li><a class="orange-bg"><img src="{{ Theme::asset('img/footer-bg.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" ><img src="{{ Theme::asset('img/footer-bg.png') }}"></a></li>');
            }else{
              $("#question-name").html(question_text);

              if (Object.keys(data.cycles).length > 1) {
                $(".chart-pagination").html('<li><a class="orange-bg" onclick="compare_cycle(1)"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" onclick="compare_cycle(2)"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');
              }
            }

            // Re assingn Filter data
            DefaultSelectAssign(FilterSelect);

          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
    }

    function next_question(move)
    {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"next_question",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle,FilterMove:move, answers: FilterSelect.answers} )
        .done(function( data ) {
          if (data != false) {

            $("#question-name").html(data.default_question.question);
            $("#select_category_label").html(data.default_question.question_categories.slice(0,20)+" ...");
            $("#select_question_label").html(data.default_question.question.slice(0,40)+" ...");

            // Re assingn Filter data
            FilterSelect.question = data.default_question.id_question;
            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }

            FilterSelect.answers = [];
            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }
            DefaultSelectAssign(FilterSelect);

            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);
            var data_points_pie_data = data_points_pie(data.question);
            chartjs(color_set_data,data_points_data,data_points_pie_data);

            // Re assign map
            dynamicRegions = data.regions;
            // Load New map
            geojson = L.geoJson(statesData, {
              style: styleDynamic,
              onEachFeature: onEachFeature,
            }).addTo(map);
          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
    }

    function find_survey_dynamic()
    {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"survey_area_dynamic",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle,answers: FilterSelect.answers} )
        .done(function( data ) {
          if (data != false) {
            // Re assingn Filter data
            FilterSelect.question = data.default_question.id_question;
            FilterSelect.answers = [];
            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }

            DefaultSelectAssign(FilterSelect);
            // Build chart
            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);
            var data_points_pie_data = data_points_pie(data.question);
            chartjs(color_set_data,data_points_data,data_points_pie_data);
          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
     }

     function detail_chart(answer_id,category_id,move)
     {
        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"detail_chart",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answer_id:answer_id, category_filter: category_id, FilterMove:move} )
          .done(function( data ) {
            if (data != false) {
              $("#chart_canvas").html('<div class="col-md-12"><div id="detailChart" style="height: 345px; width: 100%;"></div></div>');
              detail_chart_js(data.question);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
              $('.chart-pagination').html('<li><a class="orange-bg" onclick="detail_chart('+answer_id+','+data.default_question.id_category+',1)"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" onclick="detail_chart('+answer_id+','+data.default_question.id_category+',2)"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
     }

    // function compare_question(move)
    // {
    //   // Get cycles functions
    //   $.get( "filter-select", { SelectedFilter:"compare_all_cycle",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, FilterMove: move} )
    //     .done(function( data ) {
    //       if (data != false) {

    //         // Build chart
    //         $("#chart_canvas").html('<div class="col-md-12"><div id="compareChart" style="height: 345px; width: 100%;"></div></div>');

    //         var first_list = [];
    //         var end_list = [];
    //         var colorSet = [];
    //         var baseline_text = "";
    //         var endline_text = "";

    //         for (i = 0; i < data.length; i++) {
    //           if (data[i].cycle_type == 0) {
    //             baseline_text = data[i].cycle;
    //             first_list.push({ y: parseInt(data[i].amount), label: data[i].answer});

    //             colorSet.push(data[i].color);
    //           }
    //           if (data[i].cycle_type == 1) {
    //             endline_text = data[i].cycle;
    //             end_list.push({ y: parseInt(data[i].amount), label: data[i].answer});
    //           }
    //         }

    //         compare_chart(first_list,end_list, colorSet, baseline_text,endline_text);

    //         $('.chart-pagination').html('<li><a class="orange-bg" onclick="find_survey(1)><img src="{{ Theme::asset('img/footer-bg.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" ><img src="{{ Theme::asset('img/footer-bg.png') }}" onclick="find_survey(2)></a></li>');

    //         // Re assingn Filter data
    //         DefaultSelectAssign(FilterSelect);

    //       }else
    //       {
    //         alert("{{Lang::get('frontend.empty_data')}}");
    //         // Re assingn Filter data
    //         DefaultSelectAssign(DefaultSelect);
    //       }
    //     },"html");
    // }

    function color_set(assign_color)
    {
      if (assign_color != null) 
      {
        var color_set = [];
        for (var key in assign_color) {
          if (assign_color.hasOwnProperty(key)) {
            color_set.push(assign_color[key]['color']);
          }
        }
      }
      else
      {
        var color_set = [//colorSet Array
          @foreach ($question as $answer)
            "{{ $answer->color }}",
          @endforeach                 
          ];
      }

      return color_set;
    }
    function data_points(assign_answer)
    {
      if (assign_answer != null) 
      {
        var data_points = [];
        for (var key in assign_answer) {
          if (assign_answer.hasOwnProperty(key)) {
            data_points.push(
              { y: parseInt(assign_answer[key]['amount']), label: assign_answer[key]['answer'], answer_id: assign_answer[key]['id_answer'],indexLabel:assign_answer[key]['indexlabel']+"%"}
              );
          }
        }
      }
      else
      {
        var data_points = [//colorSet Array
          @foreach ($question as $answer)
            { y: {{ $answer->amount }}, label: "{{ $answer->answer }}", answer_id: "{{ $answer->id_answer }}",indexLabel: "{{ $answer->indexlabel}}%"},
          @endforeach                  
          ];
      }

      return data_points;
    }
    function data_points_pie(assign_answer)
    {
      if (assign_answer != null) 
      {
        var data_list = [];
        for (var key in assign_answer) {
          if (assign_answer.hasOwnProperty(key)) {
            data_list.push(
              { y: parseInt(assign_answer[key]['amount']), label: assign_answer[key]['answer'], answer_id: assign_answer[key]['id_answer'],indexLabel:assign_answer[key]['indexlabel']+"%"}
              );
          }
        }
        var data_points = [];
        for (i = 0; i < data_list.length; i++) {
          if (data_list[i].y != 0) {
            data_points.push(data_list[i]);    
          }
        }
      }
      else
      {
        var data_points = [//colorSet Array
          @foreach ($question as $answer)
            @if($answer->amount != 0)
              { y: {{ $answer->amount }}, label: "{{ $answer->answer }}", answer_id: "{{ $answer->id_answer }}", indexlabel: "{{ $answer->indexlabel}}%"},
            @endif
          @endforeach                  
          ];
      }

      return data_points;
    }
</script>