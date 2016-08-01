//v002
//================================================================================

var $= jQuery.noConflict();




$(window).resize(function(){
// =============================================================================
	jquerytable_transform( $( '.jquerytable' ) );
// =============================================================================
});


$(window).load(function(){
// =============================================================================
	jquerytable_transform( $( '.jquerytable' ) );
// =============================================================================
});


$(document).ready(function(){
// =============================================================================
	jquerytable_transform( $( '.jquerytable' ) );
// =============================================================================
});




function jquerytable_transform( elem )
{
	elem.each(function(){
		var table= $( this );
		table.css({ width: '100%' });
		var table_ww= table.width();
		$( '.jqt_row_tit', table ).outerWidth( table_ww );
		$( '.jqt_row_itm', table ).outerWidth( table_ww );
		var table_ww= $( '.jqt_row_tit', table ).width();
		var wwostatok= table_ww;
		$( '.jqt_row_tit .jqtrt_col', table ).each(function(){
			if( $( this ).data( 'w' ) )
			{
				var ii= $( this ).index();
				var colww= table_ww * $( this ).data( 'w' ) / 100;
				if( colww < $( this ).data( 'mw' ) ) colww= $( this ).data( 'mw' );
				$( this ).outerWidth( colww );
				wwostatok -= colww;
				$( '.jqt_row_itm', table ).each(function(){
					$( '.jqtri_col:eq('+ ii +')', this ).outerWidth( colww );
				});
			}
		});
		var autoww_cc= $( '.jqt_row_tit .jqtrtc_autoww', table ).length;
		var colwwauto= wwostatok / autoww_cc;
		var colww= colwwauto;
		var dopww= 0;
		$( '.jqt_row_tit .jqtrtc_autoww' ).each(function(index){
			var ii= $( this ).index();
			if( colww < $( this ).data( 'mw' ) ) colww= $( this ).data( 'mw' );
			$( this ).outerWidth( colww );
			if( colww > colwwauto ) dopww += colww - colwwauto;
			$( '.jqt_row_itm', table ).each(function(){
				$( '.jqtri_col:eq('+ ii +')', this ).outerWidth( colww );
			});
		});
		if( dopww )
		{
			//table.width( table_ww + dopww );
			//$( '.jqt_row_tit', table ).outerWidth( table_ww + dopww );
			//$( '.jqt_row_itm', table ).outerWidth( table_ww + dopww );
		}
		$( '.jqt_row_itm', table ).each(function(){
			//$( this ).height( $( '>div', this ).height() );
		});
		$( '.jqt_row_itm', table ).each(function(){
			//$( '.jqtri_col', this ).outerHeight( $( this ).outerHeight() );
		});
	});
}