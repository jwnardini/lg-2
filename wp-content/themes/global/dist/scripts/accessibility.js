!function(t){t(document).on("facetwp-loaded",function(){t(".facetwp-checkbox").each(function(){t(this).attr("role","checkbox"),t(this).attr("aria-checked",t(this).hasClass("checked")?"true":"false"),t(this).attr("tabindex",0),t(this).bind("keydown",function(e){32!=e.keyCode&&13!=e.keyCode||(e.stopPropagation(),e.preventDefault(),LG.LAST_FACETWP_CHECKED=t(this).data("value"),t(this).click())})}),t(".facetwp-page").each(function(){t(this).attr("tabindex",0),t(this).bind("keydown",function(e){13==e.keyCode&&(e.stopPropagation(),e.preventDefault(),t(this).click())})}),null!==LG.LAST_FACETWP_CHECKED&&t('[data-value="'+LG.LAST_FACETWP_CHECKED+'"]').focus()})}(jQuery);
//# sourceMappingURL=accessibility.js.map