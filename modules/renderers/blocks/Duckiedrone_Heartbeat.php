<?php

use \system\classes\Core;
use \system\classes\BlockRenderer;
use \system\packages\ros\ROS;


class Duckiedrone_Heartbeat extends BlockRenderer {
    
    static protected $ICON = [
        "class" => "glyphicon",
        "name" => "heart"
    ];
    
    static protected $ARGUMENTS = [
        "ros_hostname" => [
            "name" => "ROSbridge hostname",
            "type" => "text",
            "mandatory" => False,
            "default" => ""
        ],
        "topic" => [
            "name" => "ROS Topic",
            "type" => "text",
            "mandatory" => True
        ],
        "frequency" => [
            "name" => "Frequency (Hz)",
            "type" => "number",
            "default" => 10,
            "mandatory" => True
        ],
        "background_color" => [
            "name" => "Background color",
            "type" => "color",
            "mandatory" => True,
            "default" => "#fff"
        ]
    ];
    
    protected static function render($id, &$args) {
        ?>
        <div id="block_content">

            <table style="width: 100%">
                <tr>
                    <td style="width: 100%">
                        <img style="width: 100%"
                             src="<?php echo Core::getImageURL('heartbeat.gif', 'duckietown_duckiedrone') ?>">
                    </td>
                </tr>
            </table>

        </div>
        
        <?php
        $ros_hostname = $args['ros_hostname'] ?? null;
        $ros_hostname = ROS::sanitize_hostname($ros_hostname);
        $connected_evt = ROS::get_event(ROS::$ROSBRIDGE_CONNECTED, $ros_hostname);
        ?>

        <!-- Include ROS -->
        <script src="<?php echo Core::getJSscriptURL('rosdb.js', 'ros') ?>"></script>

        <script type="text/javascript">
            $(document).on("<?php echo $connected_evt ?>", function (evt) {
                // Subscribe to the CompressedImage topic
                let topic = new ROSLIB.Topic({
                    ros: window.ros['<?php echo $ros_hostname ?>'],
                    name: '<?php echo $args['topic'] ?>',
                    messageType: 'std_msgs/Empty',
                    queue_size: 1
                });

                setInterval(function () {
                    let msg = new ROSLIB.Message({});
                    topic.publish(msg);
                }, 1000 * (1.0 / <?php echo $args['frequency'] ?>))
            });

        </script>
        
        <?php
        ROS::connect($ros_hostname);
        ?>

        <style type="text/css">
            #<?php echo $id ?>{
                background-color: <?php echo $args['background_color'] ?>;
            }
        </style>
        <?php
    }//render
    
}//Duckiedrone_Heartbeat
?>
