
$(document).on('click', '.phase-title', function (event){
  event.preventDefault()
  const elem = $(this);
  const idBlock = elem.attr('data-block');

  if(elem.hasClass('passed'))
  {
    elem.removeClass('passed')
    elem.addClass('current')
    $('#' + idBlock).css('display', 'block')
  } else {
    elem.removeClass('current')
    elem.addClass('passed')
    $('#' + idBlock).css('display', 'none')
  }

})
