$(function () {
  $('.delete-modal-open').on('click', function () {
    $('.js-modal').fadeIn();
    // viewファイルより送られたsetting_reserve、setting_partを変数定義している
    var setting_reserve = $(this).attr('setting_reserve');
    var setting_part = $(this).attr('setting_part');
    // 上記で定義した変数をモーダルに表示させるために、viewファイルに返している
    $('.setting_reserve').text('予約日：' + setting_reserve);
    $('.setting_part').text('時間：リモ' + setting_part + '部');
    $('.setting_reserve_input').val(setting_reserve);
    $('.setting_part_input').val(setting_part);
    return false;
  });
  $('.js-modal-close').on('click', function () {
    $('.js-modal').fadeOut();
    return false;
  });

});
