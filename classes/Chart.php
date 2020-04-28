<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/connect.php';

class Chart {
    function __construct() {
        global $_conn;
        $this->_conn = $_conn;
    }

    /* Creates a chart */
    public function createChart($habit_id, $y_axis = 0) {
        if (empty($habit_id)) {
            return NULL;
        }

        try {
            $sql = "
                INSERT INTO `charts` (
                    type,
                    frequency,
                    compute,
                    start_date,
                    y_axis,
                    habit_id
                ) VALUES (
                    0,
                    0,
                    0,
                    0,
                    :y_axis,
                    :habit_id
                )";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':y_axis' => $y_axis,
                ':habit_id' => $habit_id
            ];
            $stmt->execute($values);
            return $this->_conn->lastInsertId();
        } catch (PDOException $e) {
            exit("Failure to create chart." . $e->getMessage());
        }
    }

    public function getChartsFromHabit($habit_id) {
        if (empty($habit_id)) {
            return NULL;
        }

        try {
            $sql = "
                SELECT
                    *
                FROM
                    `charts`
                WHERE
                    habit_id = :habit_id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':habit_id' => $habit_id
            ];
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit("Failure to get charts for habit id: " . $habit_id . "\n" . $e->getMessage());
        }
    }

    /* Returns the canvas element for the chart given */
    public function getCanvas($chart, $index, $width = 400, $height = 400) {
        if (empty($chart) || ($index < 0)) {
            return NULL;
        }

        $name = 'Chart_' . $index;
        return '<canvas id="' . $name . '" width="' . $width . '" height="' . $height . '"></canvas>';
    }

    /* Takes in a chart object and a single dimension data set 
     * PARAMS
     * $chart - a chart sql object
     * $index - number of the chart on the page (1st, 2nd, 3rd, etc)
     * $x_data - used in the charts data set, should be a 1D array
     * $x_data_labels - the labels of the $x_data, also a 1D array of the same length
     */
    public function getScript($chart, $index, $x_data, $x_data_labels) {
        if (empty($chart) || ($index < 0) || empty($x_data)) {
            return NULL;
        }

        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
        $habit_obj = new Habit;
        $habit = $habit_obj->getHabit($chart['habit_id']);

        $name = 'Chart_' . $index;

        switch ($chart['type']) {
            case 0: // line chart
                return $this->getLineScript($chart, $habit, $name, $index, $x_data, $x_data_labels);
            break;
        }
    }

    /* Creates the script code necessary to create a line chart via chart.js 
     * PARAMS:
     * $chart - the chart object used. Contains the type of x and y axis, the frequency, etc 
     * $habit - the habit of the chart
     * $name - the name of the chart to use in the js
     * $index - number of the chart on the page (1st, 2nd, 3rd, etc)
     * $x_data - used in the charts data set, should be a 1D array
     * $x_data_labels - the labels of the $x_data, also a 1D array of the same length
     */
    private function getLineScript($chart, $habit, $name, $index, $x_data, $x_data_labels) {
        // the name of the canvas grabbed is Chart_#Canvas where # is the index of the chart on the page
        // the name of the chart itself is Chart_#

        if (is_integer($x_data_labels[0])) {
            foreach ($x_data_labels as $index => $data) {
                $x_data_labels[$index] = "'" . date("M j", $data) . "'";
            }
        }

        if ($chart['y_axis'] == 0) { // completed y_axis
            $title = "Completed";
            $legend = "";
            $legend_display = "false";
            
            $tick_options = "stepSize: 1, max: 1,";
            $callback = "
            callback: function(label, index, labels) {
                switch (label) {
                    case 0:
                        return 'No';
                    case 1:
                        return 'Yes';
                }
            }";
        } else { // progress y_axis
            $title = "Progress";
            $legend = $habit['unit'];
            $legend_display = "true";
            
            $tick_options = "";
            $callback = "";
        }


        $script = "";
        $script .= "var {$name}Canvas = document.getElementById('{$name}');";
        $script .= "var {$name} = new Chart({$name}Canvas, {";
        $script .= "    type: 'line',";
        $script .= "    data: {";
        $script .= "        labels: [" . implode(", ", $x_data_labels) . "],";
        $script .= "        datasets: [{";
        $script .= "            backgroundColor: '#84CEEB',";
        $script .= "            data: [" . implode(", ", $x_data) . "],";
        $script .= "            label: '" . $legend . "'";
        $script .= "        }]";
        $script .= "    },";
        $script .= "    options: {";
        $script .= "        legend: {";
        $script .= "            display: " . $legend_display;
        $script .= "        },";
        $script .= "        responsive: false,";
        $script .= "        scales: {";
        $script .= "            yAxes: [{";
        $script .= "                ticks: {";
        $script .=                      $tick_options;
        $script .=                      $callback;
        $script .= "                }";
        $script .= "            }]";
        $script .= "        },";
        $script .= "        title: {";
        $script .= "            display: true,";
        $script .= "            text:  '" . $title . "',";
        $script .= "        },";
        $script .= "    }";
        $script .= "});";
        return $script;
    }
}

?>