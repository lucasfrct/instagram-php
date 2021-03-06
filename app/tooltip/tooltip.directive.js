angular
    .module("vhost")
    .directive("tooltip", [TooltipDirective])

function TooltipDirective(){
    return {
        restrict: "A",
        link: TooltipInit,
    }

    function TooltipInit($scope, $el) {

        $el.attr("data-position", "bottom")
        $el.attr("data-tooltip", $el.attr("tooltip"))

        var tool = M.Tooltip.init($el[0]);

    }
}