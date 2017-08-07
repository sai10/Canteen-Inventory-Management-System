function add_fields() {
	var more = '				<div class="row">\n\
				<div class="col-sm-6 col-md-3 col-lg-3">\n\
					<div class="form-group">\n\
						<select class="form-control"name="item[]">\n\ ' 
						+ ''
						+ '</select>\n\
					</div>\n\
				</div>\n\
				<div class="col-sm-6 col-md-2 col-lg-2">\n\
					<div class="form-group">\n\
							<select class="form-control" name="unit[]">\n\
							<option>kg</option>\n\
							<option>Nos.</option>\n\
						</select>\n\
					</div>\n\
				</div>\n\
				<div class="col-sm-6 col-md-2 col-lg-2">\n\
					<div class="form-group">\n\
						<input type="number" step="any" class="form-control" min="0" name="qty[]">\n\
					</div>\n\
				</div>\n\
				<div class="col-sm-6 col-md-5 col-lg-5">\n\
					<div class="row">\n\
						<div class="col-sm-10 col-md-11 col-lg-11">\n\
							<div class="form-group">\n\
								<input type="text" class="form-control" name="desc[]">\n\
							</div>\n\
						</div>\n\
						<div class="col-sm-2 col-md-1 col-lg-1">\n\
							<span class="glyphicon glyphicon-minus-sign pull-right text-danger" aria-hidden="true" onclick="$(this).parent().parent().parent().parent().remove();"></span>\n\
						</div>\n\
					</div>\n\
				</div>\n\
				';
    document.getElementById('items').innerHTML += more;
}
