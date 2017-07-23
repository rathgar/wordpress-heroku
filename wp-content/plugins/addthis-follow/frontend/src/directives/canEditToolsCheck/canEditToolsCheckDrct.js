appAddThisWordPress.directive('canEditToolsCheck', function(
  $wordpress,
  $q,
  $timeout,
  $darkseid
) {
  return {
    transclude: true,
    link: function($scope, el, attrs, ctrl, transclude) {
      var removeAlertAndTransclude = function() {
        // transclude
        var transcludeElements = el
        .find('.transclude-here-after-can-edit-tools-check');
        if (transcludeElements.length === 0) {
          // for older version of jQuery :-(
          transcludeElements = jQuery(el)
          .find('[class*="transclude-here-after-can-edit-tools-check"]');
        }
        transcludeElements.append(transclude($scope));

        // delete alert
        var deleteElements = el.find('.can-edit-tools-check-alert');
        if (deleteElements.length === 0) {
          // for older version of jQuery :-(
          deleteElements = jQuery(el)
          .find('[class*="can-edit-tools-check-alert"]');
        }
        deleteElements.remove();

        $timeout(function() {
          if (typeof window.addthis !== 'undefined') {
            window.addthis.toolbox(
              '.transclude-here-after-can-edit-tools-check'
            );
          }
        });
      };

      $wordpress.globalOptions.get().then(function(globalOptions) {
        $scope.globalOptions = globalOptions;

        if ($scope.globalOptions.addthis_plugin_controls === 'WordPress') {
          removeAlertAndTransclude();
        } else {
          var compatibilityCheck = $wordpress.compatibleWithBoost();
          var validateProfile = $darkseid.validateAddThisProfileId(
            $scope.globalOptions.addthis_profile
          );
          var validateApiKey = $wordpress.addThisApiKeyCheck(
            $scope.globalOptions.addthis_profile,
            $scope.globalOptions.api_key
          );

          $q.all([compatibilityCheck, validateProfile, validateApiKey])
          .then(function(data) {
            var compatibility = data[0];
            var profile = data[1];
            var apikey = data[2];

            if(compatibility === false) {
              $scope.alert = $scope.alerts.unsupported;
            } else if(compatibility !== true) {
              $scope.alert = $scope.alerts.genericError;
            } else if (!angular.isDefined(profile.success)) {
              $scope.alert = $scope.alerts.genericError;
            } else if (!profile.success) {
              $scope.alert = $scope.alerts.bogusProfile;
            } else if (!angular.isDefined(profile.data.type)) {
              $scope.alert = $scope.alerts.genericError;
            } else if (profile.data.type !== 'wp') {
              $scope.alert = $scope.alerts.badProfileType;
            } else if (!angular.isDefined(apikey.success)) {
              $scope.alert = $scope.alerts.genericError;
            } else if (apikey.success === false) {
              $scope.alert = $scope.alerts.badApiKey;
            } else {
              removeAlertAndTransclude();
            }
          });
        }
      },
      function() {
        $scope.alert = $scope.alerts.genericError;
      });
    },
    controller: function($scope) {
      $scope.alerts = {
        loading: {
          level: 'info',
          msgid: 'progress_message_loading'
        },
        unsupported: {
          level: 'danger',
          msgid: 'error_message_unsupported_plugin'
        },
        bogusProfile: {
          level: 'danger',
          msgid: 'error_message_invalid_profile'
        },
        badProfileType: {
          level: 'danger',
          msgid: 'error_message_wrong_profile_type'
        },
        badApiKey: {
          level: 'danger',
          msgid: 'error_message_invalid_api_key'
        },
        genericError: {
          level: 'danger',
          msgid: 'error_message_tool_check_generic'
        }
      };

      $scope.alert = $scope.alerts.loading;
    },
    templateUrl: '/directives/canEditToolsCheck/canEditToolsCheck.html'
  };
});