<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css">
</head>

<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!empty($arResult["ERROR_MESSAGE"])): ?>
  <div class="ggg" id="error-popup">
    <div class="popup">
      <h2> Вы уже голосовали</h2>
      <p> Ознакомиться с "Результаты опроса"</p>
      <button type="button" onclick="closePopup()">Ок</button>
    </div>
  </div>
<?php endif;
if (!empty($arResult["OK_MESSAGE"])): ?>
  <div class="ggg" id="success-popup">
    <div class="popup">
      <h2> Опрос пройден</h2>
      <p> Ознакомиться с "Результаты опроса"</p>
      <button type="button" onclick="closePopup()">Ок</button>
    </div>
  </div>

<?php endif;

if (empty($arResult["VOTE"])):
    return false;
elseif (empty($arResult["QUESTIONS"])):
    return true;
endif;
?>
<div class="voting-form-box">
    <form action="<?=POST_FORM_ACTION_URI?>" method="post" class="vote-form" data-mfp-src="#success-popup">
        <input type="hidden" name="vote" value="Y">
        <input type="hidden" name="PUBLIC_VOTE_ID" value="<?=$arResult["VOTE"]["ID"]?>">
        <input type="hidden" name="VOTE_ID" value="<?=$arResult["VOTE"]["ID"]?>">
        <?=bitrix_sessid_post()?>

        <ol class="vote-items-list vote-question-list">
            <?php
            $iCount = 0;
            foreach ($arResult["QUESTIONS"] as $arQuestion):
                $iCount++;
            ?>
                <li class="vote-item-vote <?=($iCount == 1 ? "vote-item-vote-first " : "")?>
                    <?=($iCount == count($arResult["QUESTIONS"]) ? "vote-item-vote-last " : "")?>
                    <?=($iCount%2 == 1 ? "vote-item-vote-odd " : "vote-item-vote-even ")?>">
                    <div class="vote-item-header">
                        <?php
                        if ($arQuestion["IMAGE"] !== false):
                        ?>
                            <div class="vote-item-image"><img src="<?=$arQuestion["IMAGE"]["SRC"]?>" width="30" height="30" /></div>
                        <?php endif; ?>
                        <div class="vote-item-title vote-item-question"><?=$arQuestion["QUESTION"]?><?php if($arQuestion["REQUIRED"]=="Y"){echo "<span class='starrequired'>*</span>";}?></div>
                        <div class="vote-clear-float"></div>
                    </div>
                    <ol class="vote-items-list vote-answers-list">
                        <?php
                        $iCountAnswers = 0;
                        foreach ($arQuestion["ANSWERS"] as $arAnswer):
                            $iCountAnswers++;
                        ?>
                            <li class="vote-item-vote <?=($iCountAnswers == 1 ? "vote-item-vote-first " : "")?>
                                <?=($iCountAnswers == count($arQuestion["ANSWERS"]) ? "vote-item-vote-last " : "")?>
                                <?=($iCountAnswers%2 == 1 ? "vote-item-vote-odd " : "vote-item-vote-even ")?>">
                                <?php
                                switch ($arAnswer["FIELD_TYPE"]):
                                    case 0: // radio
                                        $value=(isset($_REQUEST['vote_radio_'.$arAnswer["QUESTION_ID"]]) &&
                                            $_REQUEST['vote_radio_'.$arAnswer["QUESTION_ID"]] == $arAnswer["ID"]) ? 'checked="checked"' : '';
                                        break;
                                    case 1: // checkbox
                                        $value=(isset($_REQUEST['vote_checkbox_'.$arAnswer["QUESTION_ID"]]) &&
                                            array_search($arAnswer["ID"],$_REQUEST['vote_checkbox_'.$arAnswer["QUESTION_ID"]])!==false) ? 'checked="checked"' : '';
                                        break;
                                    case 2: // select
                                        $value=(isset($_REQUEST['vote_dropdown_'.$arAnswer["QUESTION_ID"]])) ? $_REQUEST['vote_dropdown_'.$arAnswer["QUESTION_ID"]] : false;
                                        break;
                                    case 3: // multiselect
                                        $value=(isset($_REQUEST['vote_multiselect_'.$arAnswer["QUESTION_ID"]])) ? $_REQUEST['vote_multiselect_'.$arAnswer["QUESTION_ID"]] : array();
                                        break;
                                    case 4: // text field
                                        $value = isset($_REQUEST['vote_field_'.$arAnswer["ID"]]) ? htmlspecialcharsbx($_REQUEST['vote_field_'.$arAnswer["ID"]]) : '';
                                        break;
                                    case 5: // memo
                                        $value = isset($_REQUEST['vote_memo_'.$arAnswer["ID"]]) ?  htmlspecialcharsbx($_REQUEST['vote_memo_'.$arAnswer["ID"]]) : '';
                                        break;
                                endswitch;
                                ?>
                                <?php
                                switch ($arAnswer["FIELD_TYPE"]):
                                    case 0: // radio
                                ?>
                                        <span class="vote-answer-item vote-answer-item-radio">
                                            <input type="radio" <?=$value?> name="vote_radio_<?=$arAnswer["QUESTION_ID"]?>" 
                                                id="vote_radio_<?=$arAnswer["QUESTION_ID"]?>_<?=$arAnswer["ID"]?>" 
                                                value="<?=$arAnswer["ID"]?>" <?=$arAnswer["~FIELD_PARAM"]?> />
                                            <label for="vote_radio_<?=$arAnswer["QUESTION_ID"]?>_<?=$arAnswer["ID"]?>"><?=$arAnswer["MESSAGE"]?></label>
                                        </span>
                                <?php
                                        break;
                                    case 1: // checkbox
                                ?>
                                        <span class="vote-answer-item vote-answer-item-checkbox">
                                            <input <?=$value?> type="checkbox" name="vote_checkbox_<?=$arAnswer["QUESTION_ID"]?>[]" value="<?=$arAnswer["ID"]?>" 
                                                id="vote_checkbox_<?=$arAnswer["QUESTION_ID"]?>_<?=$arAnswer["ID"]?>" <?=$arAnswer["~FIELD_PARAM"]?> />
                                            <label for="vote_checkbox_<?=$arAnswer["QUESTION_ID"]?>_<?=$arAnswer["ID"]?>"><?=$arAnswer["MESSAGE"]?></label>
                                        </span>
                                <?php
                                        break;
                                    case 2: // dropdown
                                ?>
                                        <span class="vote-answer-item vote-answer-item-dropdown">
                                            <select name="vote_dropdown_<?=$arAnswer["QUESTION_ID"]?>" <?=$arAnswer["~FIELD_PARAM"]?>>
                                                <option value=""><?=GetMessage("VOTE_DROPDOWN_SET")?></option>
                                                <?php foreach ($arAnswer["DROPDOWN"] as $arDropDown): ?>
                                                    <option value="<?=$arDropDown["ID"]?>" <?=($arDropDown["ID"] === $value) ? 'selected="selected"' : ''?>><?=$arDropDown["MESSAGE"]?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </span>
                                <?php
                                        break;
                                    case 3: // multiselect
                                ?>
                                        <span class="vote-answer-item vote-answer-item-multiselect">
                                            <select name="vote_multiselect_<?=$arAnswer["QUESTION_ID"]?>[]" <?=$arAnswer["~FIELD_PARAM"]?> multiple="multiple">
                                                <?php foreach ($arAnswer["MULTISELECT"] as $arMultiSelect): ?>
                                                    <option value="<?=$arMultiSelect["ID"]?>" <?= (array_search($arMultiSelect["ID"], $value) !== false) ? 'selected="selected"' : ''?>><?=$arMultiSelect["MESSAGE"]?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </span>
                                <?php
                                        break;
                                    case 4: // text field
                                ?>
                                        <span class="vote-answer-item vote-answer-item-textfield">
                                            <label for="vote_field_<?=$arAnswer["ID"]?>"><?=$arAnswer["MESSAGE"]?></label>
                                            <input type="text" name="vote_field_<?=$arAnswer["ID"]?>" id="vote_field_<?=$arAnswer["ID"]?>" 
                                                value="<?=$value?>" size="<?=$arAnswer["FIELD_WIDTH"]?>" <?=$arAnswer["~FIELD_PARAM"]?> />
                                        </span>
                                <?php
                                        break;
                                    case 5: // memo
                                ?>
                                        <span class="vote-answer-item vote-answer-item-memo">
                                            <label for="vote_memo_<?=$arAnswer["ID"]?>"><?=$arAnswer["MESSAGE"]?></label><br />
                                            <textarea name="vote_memo_<?=$arAnswer["ID"]?>" id="vote_memo_<?=$arAnswer["ID"]?>" 
                                                <?=$arAnswer["~FIELD_PARAM"]?> cols="<?=$arAnswer["FIELD_WIDTH"]?>" 
                                                rows="<?=$arAnswer["FIELD_HEIGHT"]?>"><?=$value?></textarea>
                                        </span>
                                <?php
                                        break;
                                endswitch;
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </li>
            <?php endforeach; ?>
        </ol>


        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.js"></script>

        <script>
    $(document).ready(function () {

    $('#success-popup, #error-popup').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#username',
        modal: true
    });
        $('.vote-items-list.vote-question-list').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            infinite: false,
            arrows: true,
            
        });

    });
function closePopup(){
    let ggg = document.getElementById('success-popup');
    ggg.classList.add('block');
}
</script>


        <?php if (isset($arResult["CAPTCHA_CODE"])): ?>
            <div class="vote-item-header">
                <div class="vote-item-title vote-item-question"><?=GetMessage("F_CAPTCHA_TITLE")?></div>
                <div class="vote-clear-float"></div>
            </div>
            <div class="vote-form-captcha">
                <input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
                <div class="vote-reply-field-captcha-image">
                    <img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" />
                </div>
                <div class="vote-reply-field-captcha-label">
                    <label for="captcha_word"><?=GetMessage("F_CAPTCHA_PROMT")?><span class='starrequired'>*</span></label><br />
                    <input type="text" size="20" name="captcha_word" autocomplete="off" />
                </div>
            </div>
        <?php endif; ?>

        <div class="vote-form-box-buttons vote-vote-footer">
            <span class="vote-form-box-button vote-form-box-button-first"><input type="submit" name="vote" value="<?=GetMessage("VOTE_SUBMIT_BUTTON")?>" /></span>
            <span class="vote-form-box-button vote-form-box-button-last">
                <a name="show_result" href="<?=$arResult["URL"]["RESULT"]?>"><?=GetMessage("VOTE_RESULTS")?></a>
            </span>
        </div>
    </form>
</div>