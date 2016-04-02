'use strict';

/**
 * Drag and Drop wrapper with only a functional callback interface which is used to attach callback to the event listeners
 * The only fancy thing is the "select" options and maybe the polymorph wrapping but i guess for more experienced js developers even that sucks
 *
 * @type {{dropZoneList: Array, draggableList: Array, addDropZoneCollection: Function, addDraggableCollection: Function, addDropZone: Function, initEvents: Function, addDraggable: Function, polyMorphBehavior: Function}}
 */
var DragAndDrop =
{
    /**
     * list of all registered drop target Elements
     * @var {Array}
     */
    dropZoneList: [],

    /**
     * list of all registered draggable Elements
     * @var {Array}
     */
    draggableList: [],

    /**
     * @param element
     * @param {Object} configuration
     */
    addDropZoneCollection: function (element, configuration)
    {
        var eventArray = ['dragover', 'dragenter', 'dragleave', 'drop'];
        for (var i = 0; i < element.length; i++) {
            this.initEvents(element[i], configuration, eventArray);
            this.dropZoneList.push(element[i]);
        }
    },

    /**
     * @param element
     * @param {Object} configuration
     */
    addDraggableCollection: function (element, configuration)
    {
        var eventArray = ['dragstart', 'dragend', 'drag'];
        for (var i = 0; i < element.length; i++) {
            element[i].draggable = true;
            this.initEvents(element[i], configuration, eventArray);
            this.draggableList.push(element[i]);
        }
    },

    /**
     * @param input
     * @param {Object} configuration
     */
    addDropZone: function (input, configuration)
    {
        this.polyMorphBehavior(
            input,
            configuration,
            this.addDropZoneCollection
        );
    },

    /**
     * @param {Object} element
     * @param {Object} configuration
     * @param {Array} eventArray
     *
     * @return void
     */
    initEvents: function (element, configuration, eventArray)
    {
        eventArray.map(function (eventName)
        {
            if (configuration[eventName] === undefined) {
                return;
            }
            var config = configuration[eventName];

            // no callback obviously no event attachment
            if (!config.callback) {
                return;
            }

            element.addEventListener(eventName, config.callback, false);
        }.bind(element));
    },

    /**
     * @param input
     * @param {object} configuration
     *
     * @return void
     */
    addDraggable: function (input, configuration) {
        this.polyMorphBehavior(
            input,
            configuration,
            this.addDraggableCollection
        );
    },

    /**
     * @param input
     * @param {object} configuration
     * @param {function} callback
     */
    polyMorphBehavior: function (input, configuration, callback)
    {
        if (!input || !configuration || !callback) {
            return;
        }

        var elementCollection = null;
        var type = typeof input;
        switch (type) {
            case 'object':
                if (input instanceof Element) {
                    elementCollection = [input]
                } else {
                    elementCollection = input;
                }
                break;
            case 'array':
                elementCollection = input;
                break;
            case 'string':
                elementCollection = document.querySelectorAll(input);
                break;
            default:
                elementCollection = [input];
                break;
        }


        if (!elementCollection) {
            return;
        }

        // dynamic call with self as reference otherwise "this" would be the window object
        callback.apply(this, [elementCollection, configuration]);
    }
};