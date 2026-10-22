<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Collaboration App</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- VIEW 1: Selection & Upload Screen -->
        <div id="view-selection" class="view active">
            <header class="app-header">
                <h1>Team Collaboration</h1>
                <p>Select a preloaded dataset or upload your own CSV file.</p>
            </header>

            <main class="card">
                <section class="section-group">
                    <h2>Preloaded Datasets</h2>
                    <form id="uid-form">
                        <div class="form-control">
                            <label for="file-uid-select">Choose Dataset Format:</label>
                            <select id="file-uid-select" name="UID">
                                <?php foreach ($this->cfg['filesWithUIDs'] as $index => $file): ?>
                                    <option value="<?=$index?>">
                                        <?=$file['filename']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Analyze Dataset</button>
                    </form>
                </section>

                <div class="divider"><span>OR</span></div>

                <section class="section-group">
                    <h2>Upload Custom CSV</h2>
                    <form id="upload-form">
                        <div class="file-dropzone" id="dropzone">
                            <input type="file" id="csv-file-input" name="csv_file" accept=".csv">
                            <label for="csv-file-input" id="file-label">Choose CSV file or drag & drop here</label>
                        </div>
                        <button type="submit" class="btn btn-secondary" id="upload-btn" disabled>Analyze Uploaded Dataset</button>
                    </form>
                </section>
            </main>
        </div>

        <!-- VIEW 2: Mobile App Results Screen -->
        <div id="view-results" class="view">
            <nav class="mobile-nav">
                <button id="back-btn" class="btn-back">← Back</button>
                <h2 class="breathe">Collaboration Results</h2>
                <div class="spacer"></div>
            </nav>

            <main class="results-container">
                <div id="loading-spinner" class="spinner-container hidden">
                    <div class="spinner"></div>
                    <p>Calculating overlapping periods...</p>
                </div>
                <div id="results-content">
                    <!-- Results injected dynamically -->
                </div>
            </main>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>