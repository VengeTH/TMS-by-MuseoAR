<section class="planner-tab-shell">
    <div class="planner-tab-frame-wrap">
        <iframe id="weeklyPlannerFrame" class="planner-tab-frame" src="/dashboard/weekly-planner.php" title="AI Weekly Planner"></iframe>
    </div>
</section>

<script>
(() => {
    const frame = document.getElementById("weeklyPlannerFrame");
    if (!frame) {
        return;
    }

    const resizeFrame = () => {
        try {
            const doc = frame.contentDocument || frame.contentWindow.document;
            if (!doc) {
                return;
            }
            const nextHeight = Math.max(
                doc.body ? doc.body.scrollHeight : 0,
                doc.documentElement ? doc.documentElement.scrollHeight : 0
            );
            if (nextHeight > 0) {
                frame.style.height = (nextHeight + 24) + "px";
            }
        } catch (_error) {
            // Same-origin expected in this app; ignore if inaccessible.
        }
    };

    frame.addEventListener("load", () => {
        resizeFrame();
        setTimeout(resizeFrame, 250);
        setTimeout(resizeFrame, 900);
    });

    window.addEventListener("resize", resizeFrame);
})();
</script>
