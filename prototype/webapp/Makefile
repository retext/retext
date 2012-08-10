all: bootstrap underscore backbone jquery require timeago

BOOTSTRAP_SRC=vendor/bootstrap
BOOTSTRAP_TRG=assets/bootstrap
BOOTSTRAP_LESS = ${BOOTSTRAP_SRC}/less/bootstrap.less
BOOTSTRAP_RESPONSIVE = ${BOOTSTRAP_SRC}/docs/assets/css/bootstrap-responsive.css
BOOTSTRAP_RESPONSIVE_LESS = .${BOOTSTRAP_SRC}/less/responsive.less

bootstrap:
	mkdir -p ${BOOTSTRAP_TRG}/img
	mkdir -p ${BOOTSTRAP_TRG}/css
	mkdir -p ${BOOTSTRAP_TRG}/js
	cp ${BOOTSTRAP_SRC}/img/* ${BOOTSTRAP_TRG}/img/
	recess --compile ${BOOTSTRAP_LESS} > ${BOOTSTRAP_TRG}/css/bootstrap.css
	recess --compress ${BOOTSTRAP_LESS} > ${BOOTSTRAP_TRG}/css/bootstrap.min.css
	recess --compile ${BOOTSTRAP_RESPONSIVE_LESS} > ${BOOTSTRAP_TRG}/css/bootstrap-responsive.css
	recess --compress ${BOOTSTRAP_RESPONSIVE_LESS} > ${BOOTSTRAP_TRG}/css/bootstrap-responsive.min.css
	cat ${BOOTSTRAP_SRC}/js/bootstrap-transition.js ${BOOTSTRAP_SRC}/js/bootstrap-alert.js ${BOOTSTRAP_SRC}/js/bootstrap-button.js ${BOOTSTRAP_SRC}/js/bootstrap-carousel.js ${BOOTSTRAP_SRC}/js/bootstrap-collapse.js ${BOOTSTRAP_SRC}/js/bootstrap-dropdown.js ${BOOTSTRAP_SRC}/js/bootstrap-modal.js ${BOOTSTRAP_SRC}/js/bootstrap-tooltip.js ${BOOTSTRAP_SRC}/js/bootstrap-popover.js ${BOOTSTRAP_SRC}/js/bootstrap-scrollspy.js ${BOOTSTRAP_SRC}/js/bootstrap-tab.js ${BOOTSTRAP_SRC}/js/bootstrap-typeahead.js > ${BOOTSTRAP_TRG}/js/bootstrap.js
	uglifyjs -nc ${BOOTSTRAP_TRG}/js/bootstrap.js > ${BOOTSTRAP_TRG}/js/bootstrap.min.tmp.js
	echo "/*!\n* Bootstrap.js by @fat & @mdo\n* Copyright 2012 Twitter, Inc.\n* http://www.apache.org/licenses/LICENSE-2.0.txt\n*/" > ${BOOTSTRAP_TRG}/js/copyright.js
	cat ${BOOTSTRAP_TRG}/js/copyright.js ${BOOTSTRAP_TRG}/js/bootstrap.min.tmp.js > ${BOOTSTRAP_TRG}/js/bootstrap.min.js
	rm ${BOOTSTRAP_TRG}/js/copyright.js ${BOOTSTRAP_TRG}/js/bootstrap.min.tmp.js

underscore:
	cp vendor/underscore/underscore.js assets/underscore.js
	cp vendor/underscore/underscore-min.js assets/underscore-min.js

backbone:
	cp vendor/backbone/backbone.js assets/backbone.js
	cp vendor/backbone/backbone-min.js assets/backbone-min.js

jquery:
	cp vendor/jquery/jquery-1.7.2.js assets/jquery.js
	uglifyjs -nc vendor/jquery/jquery-1.7.2.js > assets/jquery-min.js

require:
	cp vendor/require/require.js assets/require.js
	cat vendor/require/text.js >> assets/require.js
	uglifyjs -nc vendor/require/require.js > assets/require-min.js

timeago:
    cp vendor/timeago/jquery.timeago.js assets/jquery.timeago.js
    cp vendor/timeago/locales/jquery.timeago.de.js assets/jquery.timeago.de.js
