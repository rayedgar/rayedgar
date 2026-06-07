import React, { useState, useRef } from 'react';
import { cn } from './utils/cn';
import {
  Square,
  Circle,
  Type,
  Image as ImageIcon,
  Download,
  Upload,
  Eraser,
  Pencil,
  Undo,
  Redo,
  Layers,
  Settings2,
  Trash2,
  Sun,
  Contrast
} from 'lucide-react';
import Canvas from './components/Canvas';

function App() {
  const [activeTool, setActiveTool] = useState('pencil');
  const [color, setColor] = useState('#000000');
  const [brushSize, setBrushSize] = useState(5);
  const canvasRef = useRef(null);
  const fileInputRef = useRef(null);

  const handleDownload = () => {
    const dataUrl = canvasRef.current.download();
    const link = document.createElement('a');
    link.download = 'mini-photoshop-export.png';
    link.href = dataUrl;
    link.click();
  };

  const handleUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => {
        const img = new Image();
        img.onload = () => {
          canvasRef.current.uploadImage(img);
        };
        img.src = event.target.result;
      };
      reader.readAsDataURL(file);
    }
  };

  const handleClear = () => {
    if (confirm('Are you sure you want to clear the canvas?')) {
      canvasRef.current.clearCanvas();
    }
  };

  const applyFilter = (filterType) => {
    canvasRef.current.applyFilter(filterType);
  };

  return (
    <div className="flex h-screen bg-neutral-900 text-neutral-100 overflow-hidden font-sans">
      {/* Sidebar */}
      <aside className="w-16 border-r border-neutral-800 flex flex-col items-center py-4 gap-4 bg-neutral-950">
        <div className="p-2 bg-blue-600 rounded-lg mb-4">
          <ImageIcon size={24} />
        </div>

        <ToolButton
          icon={<Pencil size={20} />}
          active={activeTool === 'pencil'}
          onClick={() => setActiveTool('pencil')}
          label="Brush"
        />
        <ToolButton
          icon={<Eraser size={20} />}
          active={activeTool === 'eraser'}
          onClick={() => setActiveTool('eraser')}
          label="Eraser"
        />
        <div className="h-px w-8 bg-neutral-800 my-2" />
        <ToolButton
          icon={<Square size={20} />}
          active={activeTool === 'square'}
          onClick={() => setActiveTool('square')}
          label="Square"
        />
        <ToolButton
          icon={<Circle size={20} />}
          active={activeTool === 'circle'}
          onClick={() => setActiveTool('circle')}
          label="Circle"
        />
        <ToolButton
          icon={<Type size={20} />}
          active={activeTool === 'text'}
          onClick={() => setActiveTool('text')}
          label="Text"
        />

        <div className="mt-auto flex flex-col gap-4">
          <button
            onClick={handleClear}
            className="p-2 hover:bg-red-900/30 hover:text-red-400 rounded-lg transition-colors text-neutral-500"
            title="Clear Canvas"
          >
            <Trash2 size={20} />
          </button>
          <button className="p-2 hover:bg-neutral-800 rounded-lg transition-colors text-neutral-400" title="Settings">
            <Settings2 size={20} />
          </button>
        </div>
      </aside>

      <div className="flex-1 flex flex-col">
        {/* Top Bar */}
        <header className="h-14 border-b border-neutral-800 flex items-center px-4 justify-between bg-neutral-950">
          <div className="flex items-center gap-6">
            <div className="flex items-center gap-2">
              <span className="text-sm font-medium text-neutral-400">Size</span>
              <input
                type="range"
                min="1"
                max="50"
                value={brushSize}
                onChange={(e) => setBrushSize(parseInt(e.target.value))}
                className="w-32 h-1.5 bg-neutral-800 rounded-lg appearance-none cursor-pointer accent-blue-600"
              />
              <span className="text-xs w-6 text-neutral-500">{brushSize}px</span>
            </div>

            <div className="flex items-center gap-2">
              <span className="text-sm font-medium text-neutral-400">Color</span>
              <div className="relative group">
                <input
                  type="color"
                  value={color}
                  onChange={(e) => setColor(e.target.value)}
                  className="w-8 h-8 rounded border-0 bg-transparent cursor-pointer relative z-10"
                />
                <div
                  className="absolute inset-0 rounded border border-neutral-700"
                  style={{ backgroundColor: color }}
                />
              </div>
            </div>

            <div className="w-px h-6 bg-neutral-800 mx-1" />

            <div className="flex items-center gap-1">
              <button
                onClick={() => applyFilter('grayscale')}
                className="px-2 py-1 hover:bg-neutral-800 rounded text-xs text-neutral-400 flex items-center gap-1"
                title="Grayscale"
              >
                <Sun size={14} /> Gray
              </button>
              <button
                onClick={() => applyFilter('invert')}
                className="px-2 py-1 hover:bg-neutral-800 rounded text-xs text-neutral-400 flex items-center gap-1"
                title="Invert Colors"
              >
                <Contrast size={14} /> Invert
              </button>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button className="p-2 hover:bg-neutral-800 rounded-lg transition-colors text-neutral-500" title="Undo (Coming Soon)">
              <Undo size={18} />
            </button>
            <button className="p-2 hover:bg-neutral-800 rounded-lg transition-colors text-neutral-500" title="Redo (Coming Soon)">
              <Redo size={18} />
            </button>
            <div className="w-px h-6 bg-neutral-800 mx-1" />
            <input
              type="file"
              ref={fileInputRef}
              onChange={handleUpload}
              className="hidden"
              accept="image/*"
            />
            <button
              onClick={() => fileInputRef.current.click()}
              className="flex items-center gap-2 px-3 py-1.5 bg-neutral-800 hover:bg-neutral-700 rounded-md transition-colors text-sm"
            >
              <Upload size={16} />
              <span>Open</span>
            </button>
            <button
              onClick={handleDownload}
              className="flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 rounded-md transition-colors text-sm font-medium"
            >
              <Download size={16} />
              <span>Export</span>
            </button>
          </div>
        </header>

        {/* Workspace */}
        <main className="flex-1 bg-neutral-900 relative overflow-auto flex items-center justify-center p-8">
          <div className="bg-white shadow-2xl rounded-sm overflow-hidden border border-neutral-800 relative"
               style={{ width: '800px', height: '600px', backgroundImage: 'linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee 100%), linear-gradient(45deg, #eee 25%, white 25%, white 75%, #eee 75%, #eee 100%)', backgroundSize: '20px 20px', backgroundPosition: '0 0, 10px 10px' }}>
            <Canvas
              ref={canvasRef}
              tool={activeTool}
              color={color}
              brushSize={brushSize}
              width={800}
              height={600}
            />
          </div>
        </main>

        {/* Footer / Status Bar */}
        <footer className="h-6 border-t border-neutral-800 bg-neutral-950 px-4 flex items-center justify-between text-[10px] text-neutral-500 uppercase tracking-wider">
          <div className="flex gap-4">
            <span>800 x 600 px</span>
            <span>Tool: {activeTool}</span>
          </div>
          <div className="flex gap-4">
            <span>Mini Photoshop v1.0</span>
          </div>
        </footer>
      </div>

      {/* Layers Panel (Right Sidebar) */}
      <aside className="w-64 border-l border-neutral-800 bg-neutral-950 flex flex-col">
        <div className="p-3 border-b border-neutral-800 flex items-center justify-between">
          <span className="text-xs font-bold uppercase tracking-widest text-neutral-400">Layers</span>
          <Layers size={14} className="text-neutral-500" />
        </div>
        <div className="flex-1 overflow-auto p-2">
          <div className="flex items-center gap-2 p-2 bg-neutral-800 rounded border border-neutral-700 mb-1">
            <div className="w-10 h-10 bg-white rounded border border-neutral-600 flex-shrink-0" />
            <div className="flex-1 overflow-hidden">
              <div className="text-xs font-medium truncate">Background</div>
              <div className="text-[10px] text-neutral-500">Visible</div>
            </div>
          </div>
        </div>
      </aside>
    </div>
  );
}

function ToolButton({ icon, active, onClick, label }) {
  return (
    <button
      onClick={onClick}
      title={label}
      className={cn(
        "p-2.5 rounded-lg transition-all",
        active
          ? "bg-blue-600 text-white shadow-lg shadow-blue-900/20"
          : "text-neutral-400 hover:bg-neutral-800 hover:text-neutral-200"
      )}
    >
      {icon}
    </button>
  );
}

export default App;
