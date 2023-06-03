{\rtf1\ansi\ansicpg1252\cocoartf2636
\cocoatextscaling0\cocoaplatform0{\fonttbl\f0\fswiss\fcharset0 Helvetica;}
{\colortbl;\red255\green255\blue255;}
{\*\expandedcolortbl;;}
\margl1440\margr1440\vieww11520\viewh8400\viewkind0
\pard\tx720\tx1440\tx2160\tx2880\tx3600\tx4320\tx5040\tx5760\tx6480\tx7200\tx7920\tx8640\pardirnatural\partightenfactor0

\f0\fs24 \cf0 jQuery(document).ready(function($) \{\
  $('#convert-btn').click(function(e) \{\
    e.preventDefault();\
    var fileInput = $('#wav-file-input')[0];\
    if (fileInput.files.length === 0) \{\
      alert('Please select a WAV file.');\
      return;\
    \}\
\
    var formData = new FormData();\
    formData.append('action', 'wav_to_mp3_converter');\
    formData.append('wav_file', fileInput.files[0]);\
\
    $.ajax(\{\
      url: ajaxurl,\
      type: 'POST',\
      data: formData,\
      processData: false,\
      contentType: false,\
      beforeSend: function() \{\
        $('#conversion-status').text('Converting...');\
      \},\
      success: function(response) \{\
        if (response.success) \{\
          $('#conversion-status').text('Conversion completed.');\
          var downloadLink = '<a href="' + response.data.download_url + '">Download MP3</a>';\
          $('#download-link-container').html(downloadLink);\
        \} else \{\
          $('#conversion-status').text('Conversion failed: ' + response.data);\
        \}\
      \},\
      error: function(jqXHR, textStatus, errorThrown) \{\
        $('#conversion-status').text('Conversion failed: ' + textStatus);\
      \}\
    \});\
  \});\
\});\
}