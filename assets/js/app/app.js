const sheetId = '1sLCgW9klZLdE0A3XaWIf7E4oLzSIE1CLCNgz5LE5xCk';
const base = `https://docs.google.com/spreadsheets/d/${sheetId}/gviz/tq?`;
const sheetName = 'Matchlist';
const query = encodeURIComponent('Select *');
const url = `${base}&sheet=${sheetName}&tq=${query}`;

// document.addEventListener('DOMContentLoaded', fetch_matches);

function fetch_matches() {
  const matches = []
  const output = document.querySelector('#match_table')

  console.log("url: " + url);

  fetch(url)
    .then(res => res.text())
    .then(rep => {
      const jsonData = JSON.parse(rep.substring(47).slice(0, -2));
      console.log("rows:");
      jsonData.table.rows.forEach((rowData) => {
        const row = {};
        
        if (rowData.c[1] == null) {
          return;
        }

        if (rowData.c[7].v == false) {
          row['number'] = rowData.c[1].v;
          row['division'] = (rowData.c[3] != null) ? rowData.c[3].v : '';
          row['round'] = (rowData.c[4] != null) ? rowData.c[4].v : '';
          row['player1'] = (rowData.c[5] != null) ? rowData.c[5].v : '';
          row['player2'] = (rowData.c[6] != null) ? rowData.c[6].v : '';
          console.log(row);
          matches.push(row);
        }
      });

      const table_size = output.rows.length;
      console.log("table_size: " + table_size);
      if (table_size > 1) {
        var i = table_size;
        while (i > 1) {
          output.deleteRow(i - 1);
          i--;
        }
      }

      console.log("matches.count: " + matches.length);
      if (matches.length > 0) {
        $('#status_message').hide();
        $('#match_table').show('slow');
        matches.forEach((row) => {
          const tr = document.createElement('tr');
          const keys = ["number", "player1", "player2", "round"];
          keys.forEach((key) => {
            const td = document.createElement('td');
            td.textContent = row[key];
            tr.appendChild(td);
          });
          output.appendChild(tr);
        });
      } else {
        $('#match_table').hide();
        $('#status_message').html('Nada! All matches played!');
        $('#status_message').show('slow');
      }
    });
}

window.addEventListener('load', function() {
  update_page();
});


function update_page() {
  fetch_matches();
  setInterval(function() {
    fetch_matches();
  }, 10000);
}

