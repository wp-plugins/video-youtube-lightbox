/*  Copyright 2015 Video Youtube Lightbox  (email : manudg_1@msn.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

(function($){
$(document).ready(function(){
	
	var vyl_box = $("#vyl-box");
	var vyl_iframe = $("#vyl-iframe");
	var vyl_back = $("#vyl-back");
	var vyl_link = $(".vyl-link");
	
	var vyl_data_item = new Array();
	$(".vyl-item").each(function(index, value){
		vyl_data_item.push($(this).attr("href"));
	});
	
		/* width */
		function vyl_width()
		{
			var _width = $(window).width() * 0.8;
			vyl_box.css({width: _width+'px'});
			vyl_iframe.css({width: _width+'px'});
			vyl_back.css({width: $(window).width()});
			return _width;
		}
		
		/* height */
		function vyl_height()
		{
			var _height = $(window).height() * 0.7;
			vyl_box.css({height: _height+'px'});
			vyl_iframe.css({height: _height+'px'});
			vyl_back.css({height: $(window).height()});
			return _height;
		}
		
		/* left */
		function vyl_left()
		{
			var _left = $(window).width() - vyl_width();
			_left = _left / 2;
			vyl_box.css({left: _left+'px'});
			$(".vyl-arrow-left").css({left: 0+'px'});
			$(".vyl-arrow-right").css({right: 0+'px'});
			return _left;
		}
		
		/* top */
		function vyl_top()
		{
			var _top = $(window).height() - vyl_height();
			_top = _top / 2;
			vyl_box.css({top: _top+'px'});
			
			var _top_arrow = _top + (vyl_box.height() / 2) - ($(".vyl-arrow-left").height() / 2);
			$(".vyl-arrow-left, .vyl-arrow-right").css({top: _top_arrow+'px'});
			
			return _top;
		}
		
		/* Go to top */
		function vyl_goToLightBox()
		{
			$("html, body").animate({scrollTop:0}, '1000', 'swing');
		}
		
		/* Show the lightbox */
		vyl_link.on("click", function(e){
			vyl_position_history = $(window).scrollTop();
			vyl_box.attr('active', 'true');
			vyl_width();
			vyl_height();
			vyl_left();
			vyl_top();
			vyl_goToLightBox();
			vyl_back.fadeIn(1000);
			vyl_box.fadeIn(1000);
			if (vyl_data_item.length > 1)
			{
				$(".vyl-arrow-left, .vyl-arrow-right").fadeIn(1000);
			}
			vyl_iframe.attr('src', $(this).attr('href'));
			vyl_item = vyl_data_item.indexOf($(this).attr('href'));
			e.preventDefault();
		});
		
		/* Capture window resize event for responsive design */
		$(window).on("resize", function(){
			vyl_width();
			vyl_height();
			vyl_left();
			vyl_top();
			if (vyl_box.attr('active') == true)
			{
				vyl_goToLightBox();	
			}
		});
		
		/* fixed scroll in the top while lightbox is active */
		$(window).on("scroll", function(e){
			if (vyl_box.attr('active') == 'true')
			{
				vyl_goToLightBox();
				e.preventDefault();
				e.stopPropagation();
			}
		});
		
		/* Close the lightbox */
		vyl_back.on("click", function(){
			$(".vyl-arrow-left, .vyl-arrow-right").fadeOut(1000);
			$(this).fadeOut(1000);
			vyl_box.fadeOut(1000, function (){
				vyl_box.attr('active', 'false');
				$("html, body").animate({scrollTop:vyl_position_history}, '1000', 'swing');
			});
		});
		
		$(".vyl-arrow-left").on("click", function(){
			if (vyl_item <= 0)
			{
				vyl_item = vyl_data_item.length - 1;
			}
			else
			{
				vyl_item = vyl_item - 1;
			}
			vyl_iframe.attr('src', vyl_data_item[vyl_item]);
		});
		
		$(".vyl-arrow-right").on("click", function(){
			if (vyl_item >= vyl_data_item.length - 1)
			{
				vyl_item = 0;
			}
			else
			{
				vyl_item = vyl_item + 1;
			}
			vyl_iframe.attr('src', vyl_data_item[vyl_item]);
		});
			

})
}) (jQuery);