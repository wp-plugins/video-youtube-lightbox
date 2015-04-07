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
	
	var video_youtube_lightbox_box = $("#video-youtube-lightbox-box");
	var video_youtube_lightbox_iframe = $("#video-youtube-lightbox-iframe");
	var video_youtube_lightbox_back = $("#video-youtube-lightbox-back");
	var video_youtube_lightbox_link = $(".video-youtube-lightbox-link");
	
		/* width */
		function width()
		{
			var _width = $(window).width() * 0.8;
			video_youtube_lightbox_box.css({width: _width+'px'});
			video_youtube_lightbox_iframe.css({width: _width+'px'});
			video_youtube_lightbox_back.css({width: $(window).width()});
			return _width;
		}
		
		/* height */
		function height()
		{
			var _height = $(window).height() * 0.7;
			video_youtube_lightbox_box.css({height: _height+'px'});
			video_youtube_lightbox_iframe.css({height: _height+'px'});
			video_youtube_lightbox_back.css({height: $(window).height()});
			return _height;
		}
		
		/* left */
		function left()
		{
			var _left = $(window).width() - width();
			_left = _left / 2;
			video_youtube_lightbox_box.css({left: _left+'px'});
			return _left;
		}
		
		/* top */
		function top()
		{
			var _top = $(window).height() - height();
			_top = _top / 2;
			video_youtube_lightbox_box.css({top: _top+'px'});
			return _top;
		}
		
		/* Go to top */
		function goToLightBox()
		{
			$("html, body").animate({scrollTop:0}, '500', 'swing');
		}
		
		
		video_youtube_lightbox_link.on("click", function(e){
			e.preventDefault();
			video_youtube_lightbox_box.attr('active', 'true');
			width();
			height();
			left();
			top();
			goToLightBox();
			video_youtube_lightbox_back.fadeIn(1000);
			video_youtube_lightbox_box.fadeIn(1000);
			video_youtube_lightbox_iframe.attr('src', $(this).attr('href'));
		});
		
		$(window).on("resize", function(){
			width();
			height();
			left();
			top();
			goToLightBox();	
		});
		
		$(window).on("scroll", function(){
			if (video_youtube_lightbox_box.attr('active') == 'true')
			{
				goToLightBox();	
			}
		});
		
		video_youtube_lightbox_back.on("click", function(){
			$(this).fadeOut(1000);
			video_youtube_lightbox_box.fadeOut(1000);
			video_youtube_lightbox_box.attr('active', 'false');
		});
			

})
}) (jQuery);