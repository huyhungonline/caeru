//*------　ナビゲーション　-------*//
$(function(){
$('#gnav >ul >li').hover(function(){
	console.log('adasd');
	childPanel=$(this).children('.sub_menu');
	childPanel.each(function(){
		childPanel.css({height:'0',display:'block',opacity:'1'}).stop().animate({"min-height": "40px",opacity:'1'},600,'swing');	
	});
},function(){
	childPanel.css({display:'none'});
});
});


//*------　ポップアップ　-------*//
$(function(){
    // 「.modal-open」をクリック
    $('.modal-open').click(function(){
        // オーバーレイ用の要素を追加
        $('body').append('<div class="modal-overlay"></div>');
        // オーバーレイをフェードイン
        $('.modal-overlay').fadeIn();

        // モーダルコンテンツのIDを取得
        var modal = '#' + $(this).attr('data-target');
        // モーダルコンテンツの表示位置を設定
        modalResize();
         // モーダルコンテンツフェードイン
        $(modal).fadeIn();

        // 「.modal-overlay」あるいは「.modal-close」をクリック
        $('.modal-overlay, .modal-close').off().click(function(){
            // モーダルコンテンツとオーバーレイをフェードアウト
            $(modal).fadeOut('slow');
            $('.modal-overlay').fadeOut(function(){
                // オーバーレイを削除
                $('.modal-overlay').remove();
            });
        });

        // リサイズしたら表示位置を再取得
        $(window).on('resize', function(){
            modalResize();
        });

        // モーダルコンテンツの表示位置を設定する関数
        function modalResize(){
            // ウィンドウの横幅、高さを取得
            var w = $(window).width();
            var h = $(window).height();

            // モーダルコンテンツの表示位置を取得
            var x = (w - $(modal).outerWidth(true)) / 2;
            var y = (h - $(modal).outerHeight(true)) / 2;

            // モーダルコンテンツの表示位置を設定
            $(modal).css({'left': x + 'px','top': y + 'px'});
        }

    });
});

//*------　開閉　-------*//
$(document).ready(function(){
	
//    FixedMidashi.create();
    //クリックイベント
    $('.head').click(function(){
        //class="row"をスライドで表示/非表示する
        $(this).next('.search_box_innner').stop(true, true).slideToggle();
    });
});


//*------　勤怠データ詳細給与詳細の開閉　-------*//
$(document).ready(function(){
 	$('.salary_table').hide();
	var flg="close";
	$('.salary_btn p').click(function(){
      $('.salary_table').stop(true, true).slideToggle();
		if(flg=="close"){
			$('.salary_btn p').text('閉じる');
			$('.salary_btn p').addClass('s_size s_height btn_blue');
			flg="open";
		}else{
			$('.salary_btn p').text('開く');
			$('.salary_btn p').addClass('s_size s_height btn_blue');
			flg="close";
		}
  });  
});

//*------　 勤怠データ詳細の所定の入力　-------*//
$(function(){
    $('.box_add').click(function(){
        $('.box_add > input');
    });
    $('.box_add.on > input').blur(function(){
        $(this).hide();
//        var inputVal = $(this).val();
//        if(inputVal==''){
//            $(this).parent().text(txt);
//        } else {
//            $(this).parent().text(inputVal);
//        };
//        $(this).parent().removeClass('on');
    })
});

//*------　 ツールチップ　-------*//
$(function(){
$('.tooltip a').mouseover(function(e){
	$('body').append('<div id ="tool-panel">'+
			 $(this).children('.tool_description').html()+'</div>');
	var box_height=$('.tool_description').outerHeight();
	$('.tool_description').css('top',e.pageY - 10 - box_height);
			     $('#tool-panel').css('left',e.pageX+10);
	$('.tool_description').css('display','block');
}).mouseout(function(e){
	$('#tool-panel').remove();
	$('.tool_description').css('display','none');
	});
});


//*------　 ボタンを押してアラート　-------*//
　$(function(){
	 $('.save_btn a').click(function(){
		$('.alert_box').slideDown(300);
	 })
 })

//*------　 振休振出ポップアップ　-------*//
$(function(){
    $('.transfer_btn').click(function(){
        $('.overlay').fadeIn();
        $('.transfer').fadeIn();
    });
   $('.transfer_cloase,.overlay').click(function(){
       $('.transfer').fadeOut();
       $('.overlay').fadeOut();
//       console.log("hello");
   });
});
