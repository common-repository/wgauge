(function($) {
    'use strict';


    // UI Prototypes

    function DropDown(el) {
        "use strict";
        this.dd = el;
        this.initEvents();
    }

    function PageTile(el) {
        "use strict";
        this.pt = el;
        this.initEvents();
    }

    DropDown.prototype = {
        initEvents: function() {
            "use strict";
            var obj = this;
            obj.dd.on('click', function(event) {
                $(this).toggleClass('active');
                event.stopPropagation();
            });
        }
    }
    PageTile.prototype = {
        initEvents: function() {
            "use strict";
            var obj = this;
            obj.pt.on('click', function(event) {
                $(this).toggleClass('active');
                console.log('page-tile');
                event.stopPropagation();
            });
        }
    }

    var gk_media_init = function(selector, button_selector)  {
        console.log('test')
        var clicked_button = false;
     
        jQuery(selector).each(function (i, input) {
            var button = jQuery(input).next(button_selector);
            button.click(function (event) {
                event.preventDefault();
                var selected_img;
                clicked_button = jQuery(this);
     
                // check for media manager instance
                if(wp.media.frames.gk_frame) {
                    wp.media.frames.gk_frame.open();
                    return;
                }
                // configuration of the media manager new instance
                wp.media.frames.gk_frame = wp.media({
                    title: 'Select image',
                    multiple: false,
                    library: {
                        type: 'image'
                    },
                    button: {
                        text: 'Use selected image'
                    }
                });
     
                // Function used for the image selection and media manager closing
                var gk_media_set_image = function() {
                    var selection = wp.media.frames.gk_frame.state().get('selection');
     
                    // no selection
                    if (!selection) {
                        return;
                    }
     
                    // iterate through selected elements
                    selection.each(function(attachment) {
                        var url = attachment.attributes.url;
                        clicked_button.prev(selector).val(url);
                    });
                };
     
                // closing event for media manger
                wp.media.frames.gk_frame.on('close', gk_media_set_image);
                // image selection event
                wp.media.frames.gk_frame.on('select', gk_media_set_image);
                // showing media manager
                wp.media.frames.gk_frame.open();
            });
       });
    };

    // ON PAGE LOAD

    $(function() {
        "use strict";
        gk_media_init('.media-input', '.media-button');
        //CHART DATE PICKER HANDLER
        var start_page = moment().subtract(1, 'month');
        var end_page = moment();
        var start_site = moment().subtract(1, 'month');
        var end_site = moment();
        Chart.defaults.global.defaultFontFamily = "Biryani";
        $('.wg-settings-button').on('click',function(){
            $('.wg-modal-toggle').toggleClass('wg-modal-not-active');
        });
        var premium = false;
        
        
        $('#wg-settings-modal').on('click',function(e){
            if(e.target !== e.currentTarget) return;
            /* <fs_premium_only> */
            premium = true;
             /* </fs_premium_only> */
            if(premium){
                $('.wg-modal-toggle').addClass('wg-modal-not-active');
            } else (alert('Get premium!'));
           
        });
        
        $('#wg-settings-modal .wg-tile').on('click',function(){
            e.stopPropagation();
        });
        function siteSentiment(start_site, end_site) {
            $('#reportrange-sitesentiment span').html(start_site.format('MMMM D, YYYY') + ' - ' + end_site.format('MMMM D, YYYY'));
            let pstart = moment(start_site).format('YYYY-MM-DD'),
                pend = moment(end_site).format('YYYY-MM-DD');

            $.ajax({
                type: 'POST', // Adding Post method
                url: wgAjax.ajax_url, // Including ajax file
                data: { // Sending data dname to post_word_count function.
                    "action": "wg_query_data",
                    "start": pstart,
                    "end": pend,
                },
                // AJAX CALL SUCCESS
                success: function(data) {

                    let siteChart = document.getElementById('siteGraph').getContext('2d');
                    //drawStackedBar(data, siteGraph);
                    let grade = calculateGrades(data);

                    if (window.siteChart != undefined) {
                        window.siteChart.destroy();
					}
					//console.log('Site sentiment data:');
                    //console.log(data);
                    window.siteChart = new Chart(siteChart, {
                        type: 'bar',
                        data: {
                            labels: grade.dates,
                            datasets: [{
                                label: 'A',
                                data: grade.a,
                                backgroundColor: 'rgba(141, 195, 0, .2)',
                                borderColor: 'rgba(141, 195, 0, 1)',
                                borderWidth: 1
                            }, {
                                label: 'B',
                                data: grade.b,
                                backgroundColor: 'rgba(23, 192, 230, .2)',
                                borderColor: 'rgba(23, 192, 230, 1)',
                                borderWidth: 1
                            }, {
                                label: 'C',
                                data: grade.c,
                                backgroundColor: 'rgba(240, 220, 28, .2)',
                                borderColor: 'rgba(240, 220, 28,1)',
                                borderWidth: 1
                            }, {
                                label: 'D',
                                data: grade.d,
                                backgroundColor: 'rgba(230, 135, 25, .2)',
                                borderColor: 'rgba(230, 135, 25,1)',
                                borderWidth: 1
                            }, {
                                label: 'F',
                                data: grade.f,
                                backgroundColor: 'rgba(230, 53, 25, .2)',
                                borderColor: 'rgba(230, 53, 25,1)',
                                borderWidth: 1
                            }, ]
                        },
                        options: {

                            legend: {
                                labels: {
                                    fontColor: 'white',
                                    //fontFamily: 'Biryani'
                                }
                            },
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    ticks: {
                                        //beginAtZero:true,
                                        fontColor: 'white',
                                        fontSize: 10,

                                    },
                                    gridLines: {
                                        display: false,
                                        color: "#FFFFFF"
                                    },
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Date',
                                        fontColor: 'white'
                                    },
                                }],
                                yAxes: [{
                                    stacked: true,
                                    ticks: {
                                        beginAtZero:true,
                                        fontColor: 'white',
                                        fontSize: 10,
                                    },
                                    gridLines: {
                                        display: false,
                                        color: "#FFFFFF"
                                    },
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Gauges',
                                        fontColor: 'white'
                                    },
                                }]
                            },
                            responsive: true,
                            defaultFontColor: 'white',
                            maintainAspectRatio: false
                        }
                    });

                },
                //JSON CALL FAILED
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });

        }

        function pageSentiment(start_page, end_page) {
            $('#reportrange-pages span').html(start_page.format('MMMM D, YYYY') + ' - ' + end_page.format('MMMM D, YYYY'));
            let pstart = moment(start_page).format('YYYY-MM-DD'),
				pend = moment(end_page).format('YYYY-MM-DD'),
				page = $('.wg-page-tile--active').data('id');
            //var page_id = 7;
            $.ajax({
                type: 'POST', // Adding Post method
                url: wgAjax.ajax_url, // Including ajax file
                data: { // Sending data dname to post_word_count function.
                    "action": "wg_page_data",
                    "start": pstart,
                    "end": pend,
                    "page": page,
                },
                // AJAX CALL SUCCESS
                success: function(data) {
					console.log('Page sentiment data:');
                    console.log(data);
                    let pageChart = document.getElementById('pageGraph').getContext('2d');
                    if (window.pageChart != undefined) {
                        window.pageChart.destroy();
                    }
                    let grade = calculateGrades(data);
                    $('.wg-tile-page--title').html($('.wg-page-tile--active').data('title'));
                    window.pageChart = new Chart(pageChart, {
                        type: 'bar',
                        data: {
                            labels: grade.dates,
                            datasets: [{
                                label: 'A',
                                data: grade.a,
                                backgroundColor: 'rgba(141, 195, 0, .2)',
                                borderColor: 'rgba(141, 195, 0, 1)',
                                borderWidth: 1
                            }, {
                                label: 'B',
                                data: grade.b,
                                backgroundColor: 'rgba(23, 192, 230, .2)',
                                borderColor: 'rgba(23, 192, 230, 1)',
                                borderWidth: 1,
                            }, {
                                label: 'C',
                                data: grade.c,
                                backgroundColor: 'rgba(240, 220, 28, .2)',
                                borderColor: 'rgba(240, 220, 28,1)',
                                borderWidth: 1,
                            }, {
                                label: 'D',
                                data: grade.d,
                                backgroundColor: 'rgba(230, 135, 25, .2)',
                                borderColor: 'rgba(230, 135, 25,1)',
                                borderWidth: 1
                            }, {
                                label: 'F',
                                data: grade.f,
                                backgroundColor: 'rgba(230, 53, 25, .2)',
                                borderColor: 'rgba(230, 53, 25,1)',
                                borderWidth: 1
                            }, ]
                        },
                        options: {
							
							legend: {
								labels: {
									fontColor: 'white',
									//fontFamily: 'Biryani'
								}
							},
							scales: {
								xAxes: [{
									stacked: true,
									ticks: {
										//beginAtZero:true,
                                        fontColor: 'white',
                                        fontSize: 10,

									},
									gridLines: {
										display: false,
										color: "#FFFFFF"
									},
									scaleLabel: {
										display: true,
										labelString: 'Date',
										fontColor: 'white'
									},
								}],
								yAxes: [{
									stacked: true,
									ticks: {
										beginAtZero:true,
                                        fontColor: 'white',
                                        fontSize: 10,
									},
									gridLines: {
										display: false,
										color: "#FFFFFF"
									},
									scaleLabel: {
										display: true,
										labelString: 'Gauges',
										fontColor: 'white'
									},
								}]
							},
							responsive: true,
                            defaultFontColor: 'white',
                            maintainAspectRatio: false
						}
                    });
                    $.ajax({
                        type: 'POST', // Adding Post method
                        url: wgAjax.ajax_url, // Including ajax file
                        data: { // Sending data dname to post_word_count function.
                            "action": "wg_page_comments",
                            "start": pstart,
                            "end": pend,
                            "page": page,
                        },
                        success: function(dataComments) {
							//console.log('Comments data:');							
                            //console.log(dataComments);
                            var count = 0;
                            console.log('Before: ' + count);
                            if (dataComments.length == 0) {
                                $('#wg-page-comment-list').append('<p class="no-comment-notice">No feedback to show</p>');
                                console.log('no comments')
                            }
                            dataComments.forEach(function(obj) {
                                if (obj.feedback != null) {
                                    count++;
                                    let card = '<ul class="wg-list--row wg-list--card wg-comment-tile">';
                                    card += '<li class="wg-comment-tile--comment">';
                                    card += obj.feedback;
                                    card += '</li>';
                                    card += '<li class="wg-comment-tile--details">';
                                    card += obj.date;
                                    card += '<span>';
                                    card += obj.rating;
                                    card += '/100';
                                    card += '</span></li>';
                                    // card += '/100<div class="wg-rating-badge">';
                                    // card += obj.rating;
                                    // card += '</div></span></li>';
                                    console.log(obj.feedback);
                                    $('#wg-page-comment-list').append(card);
                                }
                                console.log('Count: ' + count)
                            });
                        },
                        error: function(errorThrown) {
                            console.log(errorThrown);
                        }
                    });

                    // for (var result in data) {
                    // 	console.log(result.date);
                    // }

                },
                //JSON CALL FAILED
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });

        }

        function calculateGrades(data) {
            let a = [],
                b = [],
                c = [],
                d = [],
                f = [],
                dates = [];
            for (var key in data) {
                dates.push(data[key].date);
                //ap.push(data[key].Ap);
                a.push(data[key].A);
                //bp.push(data[key].Bp);
                b.push(data[key].B);
                //cp.push(data[key].Cp);
                c.push(data[key].C);
                //dp.push(data[key].Dp);
                d.push(data[key].D);
                f.push(data[key].F);
            }
            var gradePackage = {
                "dates": dates,
                //"ap": ap,
                "a": a,
                //"bp" : bp,
                "b": b,
                //"cp" : cp,
                "c": c,
                //"dp" : dp,
                "d": d,
                "f": f,
            };

            return gradePackage;
        }

        $('#reportrange-sitesentiment').daterangepicker({
            startDate: start_site,
            endDate: end_site,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, siteSentiment);

        $('#reportrange-pages').daterangepicker({
            startDate: start_page,
            endDate: end_page,
            ranges: {
                //'Today': [moment(), moment()],
                //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, pageSentiment);

        $('.wg-page-tile').on('click', function() {
            $('.wg-page-tile').removeClass('wg-page-tile--active');
            //$('.wg-tile-page--title').html($(this).data('title'));
            $(this).addClass('wg-page-tile--active');
            let page = $(this).data('id');
            $('#wg-page-comment-list').html('');
            pageSentiment(start_page, end_page, page);
        });
        //var first_page = $('.wg-page-tile--active').data('id');
        siteSentiment(start_site, end_site);
        pageSentiment(start_page, end_page);

    });
})(jQuery);