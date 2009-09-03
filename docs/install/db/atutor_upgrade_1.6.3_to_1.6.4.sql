# Add folder node into `content` table
ALTER TABLE `content` add `content_type` tinyint NOT NULL DEFAULT 0;

