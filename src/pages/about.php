<style>
        /* Custom style for the fixed size elements */
        .fixed-size-iframe {
            width: 700px;
            height: 500px;
            border: 1px solid #ccc; /* Optional: Add a border to see the frame */
        }

        /* Ensure flex items don't shrink below their content size unnecessarily */
        .flex-container > * {
           flex-shrink: 0;
        }
    </style>

  <div class="container mt-5">
        <h1 class="mb-4 text-center">Unde ne gasiti si o informatie despre noi</h1>
        <div class="d-flex justify-content-center align-items-center  gap-4 flex-container">

            <!-- Google Map Embed -->
            <div style=""><iframe  class="fixed-size-iframe" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=Building%20A%20Alexandru%20Ioan%20Cuza%20University,%20Bulevardul%20Carol%20I%2011,%20Ia%C8%99i%20700506+(My%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed">

            </iframe></div>                <iframe
                    class="fixed-size-iframe"
                    src="https://www.youtube.com/embed/dQw4w9WgXcQ" /* Example video - replace with your desired video ID */
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </div>

        </div> <!-- End Flex Container -->

    </div> <!-- End Bootstrap Container -->
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Cum te folosesti de platforma noastra</h1>

        <!-- Flex container to hold items side-by-side -->
        <!-- justify-content-center: centers the items horizontally within the container -->
        <!-- gap-4: Adds space between the flex items (Bootstrap spacing utility) -->
        <!-- flex-wrap: Allows items to wrap on smaller screens if needed -->
        <div class="d-flex justify-content-center align-items-center  gap-4 flex-container">

           <video class="fixed-size-iframe" controls src="../media/video/first.mp4"></video>
        </div> <!-- End Flex Container -->

    </div> <!-- End Bootstrap Container -->
    <ul >
    <li>
     
        <audio controls preload="auto">
          <source src="../media/audio/aduio1.mp3">
        </audio>
        
      </div>
    </li>
  </ul>
 <audio src = "../media/audio/aduio1.mp3"></audio>
  <!-- Add more Bootstrap-styled content here if needed -->