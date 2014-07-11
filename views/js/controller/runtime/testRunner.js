/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define(['jquery', 'lodash',  'i18n', 'iframeNotifier', 'serviceApi/ServiceApi', 'serviceApi/UserInfoService', 'serviceApi/StateStorage'], function($, _, __, iframeNotifier, ServiceApi, UserInfoService, StateStorage) {
    'use strict';

    var Controller = {
        
        testContext: {},
        testServiceApi: null,
            
        updateItem: function(serviceCall, loading) {
            
            if (loading === true) {
                iframeNotifier.parent('loading');
            }
            
            // Markup clean-up.
            $('#item').remove();
            
            // Create new item iframe.
            var $item = $('<iframe id="item" frameborder="0" scrolling="auto"></iframe>').prependTo('body');
            
            // Adjust frame height.
            this.adjustFrame();
            
            // Inject API instance in item + serviceLoaded event callback.
            $(document).on('serviceloaded', function() {
                iframeNotifier.parent('unloading');
            });
            
            serviceCall.loadInto($item[0]);
        },
        
        nextItem: function() {
            
            iframeNotifier.parent('loading');
            var that = this;
            
            eval(this.testContext.itemServiceApi).kill(function(signal) {
                $.ajax({
                    context: that,
                    url: that.testContext['nextUrl'],
                    accepts: 'application/json',
                    cache: false,
                    contentType: 'application/json; charset=UTF-8',
                    dataType: 'json',
                    type: 'GET',
                    success: function(data, textStatus, jqXhr) {
                        this.testContext = data;
                        this.testContext.itemServiceApi = (this.testContext.itemServiceApi !== null) ? eval(this.testContext.itemServiceApi) : null;
                        
                        if (this.testContext.itemServiceApi !== null) {
                            this.updateItem(this.testContext.itemServiceApi, false);
                        }
                        else {
                            this.testServiceApi.finish();
                        }
                    }
                });
                
                iframeNotifier.parent('unloading');
            });
        },
        
        adjustFrame: function() {
            var navHeight = $('#navigation').height();
            var bodyHeight = $('body').height();
            $('#item').height(bodyHeight - navHeight);
        }
    };
    
    return {
        start: function(context) {
            
            context.itemServiceApi = eval(context.itemServiceApi);
            Controller.testContext = context;
            
            // Bindings.
            $(window).bind('resize', function() {
                Controller.adjustFrame();
            });
            
            $('#next').bind('click', function() {
               Controller.nextItem(); 
            });
            
            iframeNotifier.parent('loading');
            
            window.onServiceApiReady = function onServiceApiReady(serviceApi) {
                Controller.updateItem(context.itemServiceApi, false);
                Controller.testServiceApi = serviceApi;
            };
            
            // Notify the parent that everything is fine and ready.
            iframeNotifier.parent('serviceready');
        }
    };
});
