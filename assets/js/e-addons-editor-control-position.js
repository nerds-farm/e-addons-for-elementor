jQuery(window).on('load', function () {
    var ControlMultipleBaseItemView = elementor.modules.controls.BaseMultiple,
            ControlpoositionItemView;

    ControlpoositionItemView = ControlMultipleBaseItemView.extend({
        ui: function () {
            var ui = ControlMultipleBaseItemView.prototype.ui.apply(this, arguments);
            ui.controls = '.elementor-slider-input > input:enabled';
            ui.sliders = '.elementor-slider';
            ui.link = 'button.e-add-reset-controls';
            //ui.colors = '.elementor-shadow-color-picker';

            return ui;
        },
        events: function () {
            return _.extend(ControlMultipleBaseItemView.prototype.events.apply(this, arguments), {
                'slide @ui.sliders': 'onSlideChange',
                'click @ui.link': 'onLinkResetPosition'
            });
        },

        defaultPositionValue: {
            'x': '',
            'y': '',
        },
        onLinkResetPosition: function (event) {
            event.preventDefault();
            event.stopPropagation();


            this.ui.controls.val('');

            this.updatePositionValue();
        },

        onSlideChange: function (event, ui) {
            var type = event.currentTarget.dataset.input,
                    $input = this.ui.input.filter('[data-setting="' + type + '"]');

            $input.val(ui.value);

            //this.setValue( type, ui.value );
            //this.fillEmptyPosition();

            this.updatePosition();
        },
        /*onBeforeDestroy: function() {
         
         this.$el.remove();
         }*/
        initSliders: function () {
            var _this = this;
            var value = this.getControlValue();

            this.ui.sliders.each(function (index, slider) {
                var $slider = jQuery(this),
                        $input = $slider.next('.elementor-slider-input').find('input');

                if (elementor.config.version < '2.5') {
                    $slider.slider({
                        value: value[ this.dataset.input ],
                        min: +$input.attr('min'),
                        max: +$input.attr('max'),
                        step: +$input.attr('step')
                    });
                } else {
                    var sliderInstance = noUiSlider.create(slider, {
                        start: [value[slider.dataset.input]],
                        step: 1,
                        range: {
                            min: +$input.attr('min'),
                            max: +$input.attr('max')
                        },
                        format: {
                            to: function to(sliderValue) {
                                return +sliderValue.toFixed(1);
                            },
                            from: function from(sliderValue) {
                                return +sliderValue;
                            }
                        }
                    });


                    sliderInstance.on('slide', function (values) {
                        var type = sliderInstance.target.dataset.input;

                        $input.val(values[0]);

                        _this.setValue(type, values[0]);
                        //_this.updatePosition();
                    });

                }

            });

        },
        onReady: function () {
            this.initSliders();
            this.updatePosition();
        },

        updatePosition: function () {
            this.fillEmptyPosition();
            this.updatePositionValue();
        },
        fillEmptyPosition: function () {
            var Position = this.getPossiblePosition(),
                    $controls = this.ui.controls,
                    $sliders = this.ui.sliders,
                    defaultPositionValue = this.defaultPositionValue;

            Position.forEach(function (xyposition, index) {
                var $slider = $sliders.filter('[data-input="' + xyposition + '"]');
                var $element = $controls.filter('[data-setting="' + xyposition + '"]');

                if ($element.length && _.isEmpty($element.val())) {
                    $element.val(defaultPositionValue[xyposition]);

                    if (elementor.config.version < '2.5') {
                        $slider.slider('value', defaultPositionValue[xyposition]);
                    } else {
                        $slider[0].noUiSlider.set(defaultPositionValue[xyposition]);
                    }

                    //alert(defaultPositionValue[xyposition]);
                }

            });
        },
        updatePositionValue: function () {
            var currentValue = {},
                    Position = this.getPossiblePosition(),
                    $controls = this.ui.controls,
                    $sliders = this.ui.sliders,
                    defaultPositionValue = this.defaultPositionValue;

            Position.forEach(function (xyposition) {
                var $element = $controls.filter('[data-setting="' + xyposition + '"]');

                currentValue[ xyposition ] = $element.length ? $element.val() : defaultPositionValue;

                var $slider = $sliders.filter('[data-input="' + xyposition + '"]');

                if (elementor.config.version < '2.5') {
                    $slider.slider('value', $element.length ? $element.val() : defaultPositionValue);
                } else {
                    $slider[0].noUiSlider.set($element.length ? $element.val() : defaultPositionValue);
                }
            });
            //alert(currentValue);
            this.setValue(currentValue);
        },

        getPossiblePosition: function () {
            return [
                'x',
                'y',
            ];
        },
        onInputChange: function (event) {
            var inputSetting = event.target.dataset.setting;

            var type = event.currentTarget.dataset.setting,
                    $slider = this.ui.sliders.filter('[data-input="' + type + '"]');

            if (elementor.config.version < '2.5') {
                $slider.slider('value', this.getControlValue(type));
            } else {
                $slider[0].noUiSlider.set(this.getControlValue(type));
            }

            this.updatePosition();
        },
    });
    elementor.addControlView('position', ControlpoositionItemView);
});