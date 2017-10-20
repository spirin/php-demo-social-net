function addAjaxAction(selector, callback) {
	$(selector).click(function () {
		let link = $(this);
		if (!link.hasClass('disabled')) {
			link.addClass('disabled');
			$.getJSON(link.attr('href'), {}, function (response) {
				link.removeClass('disabled');
				if (response) {
					if (response.success) {
						callback(response.data);
					} else {
						alert(response.error);
					}
				} else {
					alert('Ошибка соединения с сервером');
				}
			});
		}
		return false;
	});
}

function showModalMessage(message, title, buttons, onShowStart, size, onShowEnd) {
	title = title || 'Сообщение';
	buttons = buttons || [{
			type: 'default',
			text: 'OK',
			callback: function (e) {
				e.modal.modal('hide');
			}
		}];
	size = size || '';

	var modalHtml = '<div class="modal fade" aria-hidden="true" tabindex="-1" role="dialog">' +
			'<div class="modal-dialog ' + size + '" role="document"><div class="modal-content">' +
			'<div class="modal-header">';

	modalHtml += '<h5 class="modal-title">' + title + '</h5>';
	modalHtml += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>';
	
	modalHtml += '</div><div class="modal-body">';
	modalHtml += message;
	modalHtml += '</div><div class="modal-footer">';
	modalHtml += '</div></div></div></div>';
	var modal = $(modalHtml);
	$('body').append(modal);

	var buttonsContainer = modal.find('.modal-footer');
	for (var b = 0; b < buttons.length; b++) {
		var btn = $('<button type="button" class="btn btn-' + buttons[b].type + ' btn-sm">' + buttons[b].text + '</button>');
		buttonsContainer.append(btn);
		var callback = buttons[b].callback;
		if (callback) {
			(function (cb, b) {
				b.on('click', function (e) {
					e.modal = modal;
					cb(e);
				});
			})(callback, btn);
		}
	}

	modal.modal({
		show: true
	});

	if (onShowStart) {
		onShowStart(modal);
	}

	if (onShowEnd) {
		modal.on('shown.bs.modal', onShowEnd);
	}

	modal.on('hidden.bs.modal', function () {
		modal.remove();
	});

	return false;
}
;