import React, { useRef, useEffect, useState, useImperativeHandle, forwardRef } from 'react';

const Canvas = forwardRef(({ tool, color, brushSize, width = 800, height = 600 }, ref) => {
  const canvasRef = useRef(null);
  const contextRef = useRef(null);
  const [isDrawing, setIsDrawing] = useState(false);
  const [startPos, setStartPos] = useState({ x: 0, y: 0 });
  const [snapshot, setSnapshot] = useState(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    canvas.width = width * 2;
    canvas.height = height * 2;
    canvas.style.width = `${width}px`;
    canvas.style.height = `${height}px`;

    const context = canvas.getContext('2d');
    context.scale(2, 2);
    context.lineCap = 'round';
    context.lineJoin = 'round';
    context.strokeStyle = color;
    context.lineWidth = brushSize;
    contextRef.current = context;

    // Set background to white
    context.fillStyle = 'white';
    context.fillRect(0, 0, width, height);
  }, []);

  useEffect(() => {
    if (contextRef.current) {
      contextRef.current.strokeStyle = tool === 'eraser' ? 'white' : color;
      contextRef.current.lineWidth = brushSize;
    }
  }, [color, brushSize, tool]);

  useImperativeHandle(ref, () => ({
    clearCanvas: () => {
      const context = contextRef.current;
      context.fillStyle = 'white';
      context.fillRect(0, 0, width, height);
    },
    download: () => {
      const canvas = canvasRef.current;
      return canvas.toDataURL('image/png');
    },
    uploadImage: (img) => {
      const context = contextRef.current;
      context.drawImage(img, 0, 0, width, height);
    },
    applyFilter: (filterType) => {
      const canvas = canvasRef.current;
      const context = contextRef.current;
      const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
      const data = imageData.data;

      for (let i = 0; i < data.length; i += 4) {
        if (filterType === 'grayscale') {
          const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
          data[i] = avg;
          data[i + 1] = avg;
          data[i + 2] = avg;
        } else if (filterType === 'invert') {
          data[i] = 255 - data[i];
          data[i + 1] = 255 - data[i + 1];
          data[i + 2] = 255 - data[i + 2];
        }
      }
      context.putImageData(imageData, 0, 0);
    },
    drawText: (text, x, y, color, size) => {
      const context = contextRef.current;
      context.fillStyle = color;
      context.font = `${size}px sans-serif`;
      context.fillText(text, x, y);
    }
  }));

  const getMousePos = (e) => {
    const rect = canvasRef.current.getBoundingClientRect();
    return {
      x: e.clientX - rect.left,
      y: e.clientY - rect.top
    };
  };

  const startDrawing = (e) => {
    const { x, y } = getMousePos(e);
    setStartPos({ x, y });
    setIsDrawing(true);

    if (tool === 'pencil' || tool === 'eraser') {
      contextRef.current.beginPath();
      contextRef.current.moveTo(x, y);
    } else {
      // For shapes, save a snapshot
      const canvas = canvasRef.current;
      setSnapshot(contextRef.current.getImageData(0, 0, canvas.width, canvas.height));
    }
  };

  const draw = (e) => {
    if (!isDrawing) return;
    const { x, y } = getMousePos(e);

    if (tool === 'pencil' || tool === 'eraser') {
      contextRef.current.lineTo(x, y);
      contextRef.current.stroke();
    } else {
      // Redraw snapshot before drawing new shape preview
      contextRef.current.putImageData(snapshot, 0, 0);

      if (tool === 'square') {
        contextRef.current.strokeRect(startPos.x, startPos.y, x - startPos.x, y - startPos.y);
      } else if (tool === 'circle') {
        contextRef.current.beginPath();
        const radius = Math.sqrt(Math.pow(x - startPos.x, 2) + Math.pow(y - startPos.y, 2));
        contextRef.current.arc(startPos.x, startPos.y, radius, 0, 2 * Math.PI);
        contextRef.current.stroke();
      }
    }
  };

  const stopDrawing = (e) => {
    if (isDrawing) {
      if (tool === 'text') {
        const { x, y } = getMousePos(e);
        const text = prompt('Enter text:');
        if (text) {
          const context = contextRef.current;
          context.fillStyle = color;
          context.font = `${brushSize * 2}px sans-serif`;
          context.fillText(text, x, y);
        }
      } else if (tool === 'pencil' || tool === 'eraser') {
        contextRef.current.closePath();
      }
    }
    setIsDrawing(false);
  };

  return (
    <canvas
      onMouseDown={startDrawing}
      onMouseMove={draw}
      onMouseUp={stopDrawing}
      onMouseLeave={stopDrawing}
      ref={canvasRef}
      className="cursor-crosshair block"
    />
  );
});

export default Canvas;
