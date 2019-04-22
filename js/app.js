var lastId,
    topMenu = $(".menu_nav"),
	arrows = $(".index__footer"),
    topMenuHeight = topMenu.outerHeight()+100,
    menuItems = topMenu.find("a"),
	arrowItems = arrows.find("a");
  //  scrollItems = menuItems.map(function(){
  //   var item = $($(this).attr("href"));
  //   if (item.length) { return item; }
  // });
 
//menuItems.click(function(e){
//  var href = $(this).attr("href"),
//      offsetTop = href === "#" ? 0 : $(href).offset().top-topMenuHeight+1;
//  $('html, body').stop().animate({ 
//      scrollTop: offsetTop
//  }, 300);
//  e.preventDefault();
//});

arrowItems.click(function(e){
  var href = $(this).attr("href"),
      offsetTop = href === "#" ? 0 : $(href).offset().top-topMenuHeight+50;
  $('html, body').stop().animate({ 
      scrollTop: offsetTop
  }, 300);
  e.preventDefault();
});
 
//$(window).scroll(function(){
//   var fromTop = $(this).scrollTop()+topMenuHeight;
//   var cur = scrollItems.map(function(){
//     if ($(this).offset().top < fromTop)
//       return this;
//   });
//   cur = cur[cur.length-1];
//   var id = cur && cur.length ? cur[0].id : "";
//   
//   if (lastId !== id) {
//       lastId = id;
//	   console.log(id);
//       menuItems
//         .parent().removeClass("menu__item_active")
//         .end().filter("[href='#"+id+"']").parent().addClass("menu__item_active");
//	   menuItems
//         .removeClass("menu__link_active")
//         .filter("[href='#"+id+"']").addClass("menu__link_active");
//   }                   
//});

function go(url){
	window.open = url;
}

function loc(url){
	window.location.href = url;
}